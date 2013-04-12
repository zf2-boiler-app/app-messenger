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
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	private $styleInliner;

	/**
	 * @var \Zend\Mvc\Router\RouteStackInterface
	 */
	private $router;

	/**
	 * @var array<\Zend\View\Renderer\RendererInterface>
	 */
	private $renderers;

	/**
	 * @var array<\Zend\Mail\Transport\TransportInterface>
	 */
	private $transporters = array();

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

		foreach(array_unique($aMedias) as $sMedia){
			switch($sMedia){
				case self::MEDIA_EMAIL:
					//Format message for email transporter
					$oMessage = $this->formatMessageForMedia($oMessage, $sMedia);

					//Retrieve transporter
					$oTransporter = $this->getTransporter(self::MEDIA_EMAIL);

					//Retrieve le renderer
					$oRenderer = $this->getRenderer(self::MEDIA_EMAIL);

					//InlineStyle
					$oStyleInliner = $this->getStyleInliner();

					$oRenderer->layout()->subject = $oMessage->getSubject();
					$oRenderer->layout()->content = $oMessage->getBodyText();
					$this->renderView($oRenderer->layout(),function($sHtml) use($oMessage,$oTransporter,$oStyleInliner){
						$oTransporter->send($oMessage->setBody($oStyleInliner->processHtml($sHtml)));
					});
					break;
				default:
					throw new \DomainException('Media "'.$sMedia.'" is not a valid media');
			}
		}
		return $this;
	}

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \UnexpectedValueException
	 * @throws \DomainException
	 * @return \BoilerAppMessenger\Mail\Message
	 */
	protected function formatMessageForMedia(\BoilerAppMessenger\Message $oMessage,$sMedia){
		if(!is_string($sMedia))throw new \InvalidArgumentException('Media expects string, "'.gettype($sMedia).'" given');

		switch($sMedia){
			case self::MEDIA_EMAIL:
				$oFormatMessage = new \BoilerAppMessenger\Mail\Message();
				$oFormatMessage->setEncoding('UTF-8');

				//From Sender
				$oFrom = $oMessage->getFrom();
				if($oFrom === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->setFrom(
					$this->getOptions()->getSystemUserEmail(),
					$this->getOptions()->getSystemUserName()
				);
				elseif($oFrom instanceof \BoilerAppUser\Entity\UserEntity)$oFormatMessage->setFrom(
					$oFrom->getUserAuthAccess()->getAuthAccessEmailIdentity(),
					$oFrom->getUserDisplayName()
				);
				else throw new \UnexpectedValueException(sprintf(
					'"From" sender expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity, "%s" given',
					is_scalar($oFrom)?$oFrom:(is_object($oFrom)?get_class($oFrom):gettype($oFrom))
				));

				//To Recipiants
				foreach($oMessage->getTo() as $oTo){
					if($oTo === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->addTo(
						$this->getOptions()->getSystemUserEmail(),
						$this->getOptions()->getSystemUserName()
					);
					elseif($oTo instanceof \BoilerAppUser\Entity\UserEntity)$oFormatMessage->addTo(
						$oTo->getUserAuthAccess()->getAuthAccessEmailIdentity(),
						$oTo->getUserDisplayName()
					);
					else throw new \UnexpectedValueException('"To" Recipiant expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity');
				}

				//Subject
				$oFormatMessage->setSubject($oMessage->getSubject());

				//Body
				$oFormatMessage->setBody($oMessage->getBody());
				break;
			default:
				throw new \DomainException('Media "'.$sMedia.'" is not a valid media');
		}
		return $oFormatMessage;
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
	 * Retrieve media renderer
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \DomainException
	 * @return \Zend\View\Renderer\RendererInterface
	 */
	protected function getRenderer($sMedia){
		if(!is_string($sMedia))throw new \InvalidArgumentException('Media expects string, "'.gettype($sMedia).'" given');
		if(isset($this->renderers[$sMedia]) && $this->renderers[$sMedia] instanceof \Zend\View\Renderer\RendererInterface)return $this->renderers[$sMedia];
		switch($sMedia){
			//Renderer for single view
			case 'default':
				$this->renderers[$sMedia] = new \Zend\View\Renderer\PhpRenderer();
				$this->renderers[$sMedia]->setResolver(new \Zend\View\Resolver\TemplateMapResolver($this->getOptions()->hasTemplateMap()?$this->getOptions()->getTemplateMap():null));
				break;

			//Renderer for email
			case self::MEDIA_EMAIL:
				$this->renderers[$sMedia] = new \BoilerAppMessenger\View\Renderer\EmailRenderer();

				//Create layout template
				$oLayout = new \Zend\View\Model\ViewModel();
				$oEvent = new \Zend\Mvc\MvcEvent(\Zend\Mvc\MvcEvent::EVENT_RENDER);
				$this->getTemplatingService()->buildLayoutTemplate($oEvent
					->setRequest(new \Zend\Http\Request())
					->setViewModel($oLayout)
				);

				$this->renderers[$sMedia]->setResolver(
					new \Zend\View\Resolver\TemplateMapResolver($this->getOptions()->hasTemplateMap()?$this->getOptions()->getTemplateMap():null)
				)->plugin('view_model')->setRoot($oEvent->getViewModel());
				break;

			default:
				throw new \DomainException('Media "'.$sMedia.'" is not a defined media');
		}

		//Add mandatory helpers
		$oTranslateHelper = new \Zend\I18n\View\Helper\Translate();
		$this->renderers[$sMedia]->getHelperPluginManager()->setService(
			'translate',
			$oTranslateHelper->setTranslator($this->getTranslator())->setTranslatorEnabled(true)
		);

		$oUrlHelper = new \Zend\View\Helper\Url();
		$this->renderers[$sMedia]->getHelperPluginManager()->setService(
			'url',
			$oUrlHelper->setRouter($this->getRouter())
		);
		return $this->renderers[$sMedia];
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
	private function getOptions(){
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
	private function getAssetsBundleService(){
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
	private function getTemplatingService(){
		if($this->templatingService instanceof \TreeLayoutStack\TemplatingService)return $this->templatingService;
		throw new \LogicException('Templating Service is undefined');
	}

	/**
	 * @param \BoilerAppMessenger\StyleInliner\StyleInlinerService $oStyleInliner
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setStyleInliner(\BoilerAppMessenger\StyleInliner\StyleInlinerService $oStyleInliner){
		$this->styleInliner = $oStyleInliner;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	private function getStyleInliner(){
		if($this->styleInliner instanceof \BoilerAppMessenger\StyleInliner\StyleInlinerService)return $this->styleInliner;
		throw new \LogicException('StyleInliner is undefined');
	}

	/**
	 * @param \Zend\Mail\Transport\TransportInterface $oTransporter
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setTransporter(\Zend\Mail\Transport\TransportInterface $oTransporter,$sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		$this->transporters[$sMedia] = $oTransporter;
		return $this;
	}

	/**
	 * Retrieve media transporter
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return \Zend\Mail\Transport\TransportInterface
	 */
	private function getTransporter($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->transporters[$sMedia]) && $this->transporters[$sMedia] instanceof \Zend\Mail\Transport\TransportInterface)return $this->transporters[$sMedia];
		else throw new \LogicException('Transporter is not defined for media "'.$sMedia.'"');
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