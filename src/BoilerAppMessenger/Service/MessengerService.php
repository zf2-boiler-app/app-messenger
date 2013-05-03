<?php
namespace BoilerAppMessenger\Service;
class MessengerService implements \Zend\I18n\Translator\TranslatorAwareInterface{
	use \Zend\I18n\Translator\TranslatorAwareTrait;

	const MEDIA_EMAIL = 'email';

	/**
	 * @var \BoilerAppMessenger\Service\MessengerOptions
	 */
	private $options;

	/**
	 * @var \AssetsBundle\Service\Service
	 */
	private $assetsBundleService;

	/**
	 * @var \TreeLayoutStack\TemplatingService
	 */
	private $templatingService;

	/**
	 * @var \Zend\Mvc\Router\RouteStackInterface
	 */
	private $router;

	/**
	 * @var array<\BoilerAppMessenger\MessageAdapterInterface>
	 */
	private $messageAdapters = array();

	/**
	 * @var array<\BoilerAppMessenger\MessageRendererInterface>
	 */
	private $messageRenderers;

	/**
	 * @var array<\BoilerAppMessenger\MessageTransporterInterface>
	 */
	private $messageTransporters = array();

	/**
	 * Constructor
	 * @param \BoilerAppMessenger\Service\MessengerOptions $oOptions
	 */
	private function __construct(\BoilerAppMessenger\Service\MessengerOptions $oOptions = null){
		if($oOptions)$this->setOptions($oOptions);
	}

	/**
	 * Instantiate a messenger service
	 * @param array|Traversable $aOptions
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public static function factory($aOptions){
		if($aOptions instanceof \Traversable)$aOptions = \Zend\Stdlib\ArrayUtils::iteratorToArray($aOptions);
		elseif(!is_array($aOptions))throw new \InvalidArgumentException(__METHOD__.' expects an array or Traversable object; received "'.(is_object($aOptions)?get_class($aOptions):gettype($aOptions)).'"');
		return new static(new \BoilerAppMessenger\Service\MessengerOptions($aOptions));
	}

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @param string|array $aMedias
	 * @throws \InvalidArgumentException
	 * @throws \DomainException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function sendMessage(\BoilerAppMessenger\Message $oMessage,$aMedias){
		if(empty($aMedias))throw new \InvalidArgumentException('A media must be specified');
		elseif(is_string($aMedias))$aMedias = array($aMedias);
		elseif(!is_array($aMedias))throw new \InvalidArgumentException('$aMedias expects an array or a string, "'.gettype($aMedias).'" given');

		foreach(array_filter(array_unique($aMedias)) as $sMedia){
			//Retrieve transporter
			$oTransporter = $this->getTransporter($sMedia);

			$this->getRenderer($sMedia)->renderMessage($oMessage,function($oMessage){
				$oTransporter->send($oMessage);
			});
		}
		return $this;
	}

	/**
	 * Render single view
	 * @param \Zend\View\Model\ViewModel $oView
	 * @param $oCallback : callback function
	 * @throws \BadFunctionCallException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function renderView(\Zend\View\Model\ViewModel $oView,$oCallback){
		if(!is_callable($oCallback))throw new \BadFunctionCallException('$oCallback is not a callable');

		$oRenderer = $this->getRenderer('default');
		$oRenderer->plugin('view_model')->setRoot($oView);
		$oMessageView = new \Zend\View\View();
		$oMessageView->setResponse(new \Zend\Stdlib\Response());
		$oMessageView->getEventManager()->attach(new \Zend\View\Strategy\PhpRendererStrategy($oRenderer));

		//Manage assets
		$oAssetsBundleService = $this->getAssetsBundleService();
		$oMessageView->getEventManager()->attach(
			\Zend\View\ViewEvent::EVENT_RENDERER,
			function(\Zend\View\ViewEvent $oEvent) use($oAssetsBundleService, $oRenderer){
				$oAssetsBundleService->setRenderer($oRenderer)->setControllerName(current(explode('\\',__NAMESPACE__)))->renderAssets();
			}
		);

		//Process after rendering
		$oMessageView->getEventManager()->attach(\Zend\View\ViewEvent::EVENT_RESPONSE,function(\Zend\View\ViewEvent $oEvent) use($oCallback){
			call_user_func($oCallback,$oEvent->getResult());
		});
		$oMessageView->render($oRenderer->layout());
		return $this;
	}

	/**
	 * @param \BoilerAppMessenger\Service\MessengerOptions $oOptions
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setOptions(\BoilerAppMessenger\Service\MessengerOptions $oOptions){
		$this->options = $oOptions;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\Service\MessengerOptions
	 */
	protected function getOptions(){
		if($this->options instanceof \BoilerAppMessenger\Service\MessengerOptions)return $this->options;
		throw new \LogicException('Options are undefined');
	}

	/**
	 * @param \AssetsBundle\Service\Service
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setAssetsBundleService(\AssetsBundle\Service\Service $oAssetsBundleService){
		$this->assetsBundleService = $oAssetsBundleService;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \AssetsBundle\Service\Service
	 */
	protected function getAssetsBundleService(){
		if($this->assetsBundleService instanceof \AssetsBundle\Service\Service)return $this->assetsBundleService;
		throw new \LogicException('AssetsBundle Service is undefined');
	}

	/**
	 * @param \TreeLayoutStack\TemplatingService $oTemplatingService
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setTemplatingService(\TreeLayoutStack\TemplatingService $oTemplatingService){
		$this->templatingService = $oTemplatingService;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \TreeLayoutStack\TemplatingService
	 */
	protected function getTemplatingService(){
		if($this->templatingService instanceof \TreeLayoutStack\TemplatingService)return $this->templatingService;
		throw new \LogicException('Templating Service is undefined');
	}

	/**
	 * @param \BoilerAppMessenger\MessageAdapter\MessageAdapterInterface $oMessageAdapter
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setMessageAdapter(\BoilerAppMessenger\MessageAdapter\MessageAdapterInterface $oMessageAdapter,$sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		$this->messageAdapters[$sMedia] = $oMessageAdapter;
		return $this;
	}

	/**
	 * Retrieve message adapter for given media
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\MessageAdapterInterface
	 */
	protected function getMessageAdapter($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->messageAdapters[$sMedia]) && $this->messageAdapters[$sMedia] instanceof \BoilerAppMessenger\MessageAdapterInterface)return $this->messageAdapters[$sMedia];
		else throw new \LogicException('Message adapter is not defined for media "'.$sMedia.'"');
	}

	/**
	 * @param \BoilerAppMessenger\MessageTransporterInterface $oMessageTransporter
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setMessageTransporter(\BoilerAppMessenger\MessageTransporterInterface $oMessageTransporter,$sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		$this->messageTransporters[$sMedia] = $oMessageTransporter;
		return $this;
	}

	/**
	 * Retrieve message transporter for given media
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\MessageTransporterInterface
	 */
	protected function getTransporter($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->messageTransporters[$sMedia]) && $this->messageTransporters[$sMedia] instanceof \BoilerAppMessenger\MessageTransporterInterface)return $this->messageTransporters[$sMedia];
		else throw new \LogicException('Message transporter is not defined for media "'.$sMedia.'"');
	}

	/**
	 * @param \BoilerAppMessenger\MessageRendererInterface $oMessageRenderer
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setRenderer(\BoilerAppMessenger\MessageRendererInterface $oMessageRenderer,$sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		$this->messageRenderers[$sMedia] = $oMessageRenderer;
		return $this;
	}

	/**
	 * Retrieve message renderer for given media
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\MessageRendererInterface
	 */
	protected function getRenderer($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->messageRenderers[$sMedia]) && $this->messageRenderers[$sMedia] instanceof \BoilerAppMessenger\MessageRendererInterface)return $this->messageRenderers[$sMedia];
		else throw new \LogicException('Message renderer is not defined for media "'.$sMedia.'"');
	}

	/**
	 * @param \Zend\Mvc\Router\RouteStackInterface $oRouter
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setRouter(\Zend\Mvc\Router\RouteStackInterface $oRouter){
		$this->router = $oRouter;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \Zend\Mvc\Router\RouteStackInterface
	 */
	private function getRouter(){
		if($this->router instanceof \Zend\Mvc\Router\RouteStackInterface)return $this->router;
		throw new \LogicException('Router is undefined');
	}
}