<?php
namespace BoilerAppMessenger\Service;
class MessengerService implements \Zend\EventManager\SharedEventManagerAwareInterface, \Zend\I18n\Translator\TranslatorAwareInterface{
	//use \Zend\I18n\Translator\TranslatorAwareTrait;

	const MEDIA_EMAIL = 'email';

	/**
	 * @var array
	 */
	private $configuration;

	/**
	 * @var \AssetsBundle\Service\Service
	 */
	private $assetsBundleService;

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
	 * @var \Zend\EventManager\SharedEventManagerInterface
	 */
	protected $sharedEventManager;

	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \InvalidArgumentException
	 */
	private function __construct(array $aConfiguration){
		if(
			!isset($aConfiguration['system_user']['email'],$aConfiguration['system_user']['name'],$aConfiguration['transporters'])
			|| ($aConfiguration['system_user']['email'] = filter_var($aConfiguration['system_user']['email'],FILTER_VALIDATE_EMAIL)) === false
			|| !is_array($aConfiguration['transporters'])
		)throw new \InvalidArgumentException('Messenger Service configuration is not valid');

		//Set transporters
		foreach($aConfiguration['transporters'] as $sMedia => $oTransporter){
			$this->setTransporter($oTransporter, $sMedia);
		}
		unset($aConfiguration['transporters']);
		$this->configuration = $aConfiguration;
	}

	/**
	 * Instantiate a messenger
	 * @param array|Traversable $aConfiguration
	 * @param \AssetsBundle\Service\Service $oAssetsBundleService
	 * @param \Messenger\Mail\InlineStyle\InlineStyleService $oInlineStyle
	 * @param \Zend\I18n\Translator\Translator $oTranslator
	 * @param \Zend\Mvc\Router\RouteStackInterface $oRouter
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public static function factory($aConfiguration,
		\AssetsBundle\Service\Service $oAssetsBundleService,
		\BoilerAppMessenger\Mail\InlineStyle\InlineStyleService $oInlineStyle,
		\Zend\I18n\Translator\Translator $oTranslator,
		\Zend\Mvc\Router\RouteStackInterface $oRouter
	){
		if($aConfiguration instanceof \Traversable)$aConfiguration = \Zend\Stdlib\ArrayUtils::iteratorToArray($aConfiguration);
		elseif(!is_array($aConfiguration))throw new \InvalidArgumentException(__METHOD__.' expects an array or Traversable object; received "'.(is_object($aConfiguration)?get_class($aConfiguration):gettype($aConfiguration)).'"');
		$oMessengerService = new static($aConfiguration);
		return $oMessengerService
		->setAssetsBundleService($oAssetsBundleService)
		->setInlineStyle($oInlineStyle)
		->setTranslator($oTranslator)
		->setRouter($oRouter);
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
		elseif(!is_array($aMedias))throw new \InvalidArgumentException('$aMedias expects an array or a string');
		foreach(array_unique($aMedias) as $sMedia){
			switch($sMedia){
				case self::MEDIA_EMAIL:
					//Format message for email transporter
					$oMessage = $this->formatMessageForMedia($oMessage, $sMedia);

					//Retrieve transporter
					$oTransporter = $this->getTransporter(self::MEDIA_EMAIL);

					//Retrieve le renderer
					$oRenderer = $this->getRenderer(self::MEDIA_EMAIL);

					//Header view
					$oHeaderView = new \Zend\View\Model\ViewModel(array('subject' => $oMessage->getSubject()));

					//Content view
					$oContentView = new \Zend\View\Model\ViewModel(array('content'=> $oMessage->getBodyText()));

					//InlineStyle
					$oStyleInliner = $this->getStyleInliner();

					$oRenderer->layout()->subject = $oMessage->getSubject();
					$oRenderer->layout()->addChild($oHeaderView->setTemplate('email/header'), 'header')->addChild($oContentView->setTemplate('email/default'));
					return $this->renderView($oRenderer->layout(),function($sHtml) use($oMessage,$oTransporter,$oStyleInliner){
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
	 * @throws \UnexpectedValueException
	 * @throws \DomainException
	 * @return \BoilerAppMessenger\Mail\Message
	 */
	protected function formatMessageForMedia(\BoilerAppMessenger\Message $oMessage,$sMedia){
		switch($sMedia){
			case self::MEDIA_EMAIL:
				$oFormatMessage = new \BoilerAppMessenger\Mail\Message();
				$oFormatMessage->setEncoding('UTF-8');

				//From Sender
				$oFrom = $oMessage->getFrom();
				if($oFrom === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->setFrom(
					$this->configuration['system_user']['email'],
					$this->configuration['system_user']['name']
				);
				elseif($oFrom instanceof \User\Entity\UserEntity)$oFormatMessage->setFrom($oFrom->getUserAuthAccess()->getAuthAccessEmailIdentity());
				else throw new \UnexpectedValueException(sprintf(
					'"From" sender expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity, "%s" given',
					is_scalar($oFrom)?$oFrom:(is_object($oFrom)?get_class($oFrom):gettype($oFrom))
				));

				//To Recipiants
				foreach($oMessage->getTo() as $oTo){
					if($oTo === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->addTo(
						$this->configuration['system_user']['email'],
						$this->configuration['system_user']['name']
					);
					elseif($oTo instanceof \User\Entity\UserEntity)$oFormatMessage->addTo($oTo->getUserAuthAccess()->getAuthAccessEmailIdentity());
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
	 * @param \Closure $oCallback
	 * @throws \BadFunctionCallException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function renderView(\Zend\View\Model\ViewModel $oView,\Closure $oCallback){
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
				$oAssetsBundleService->setRenderer($oRenderer)->setControllerName('email')->renderAssets(array(
					'application',
					'messenger'
				));
			}
		);

		//Process after rendering
		$oMessageView->getEventManager()->attach(\Zend\View\ViewEvent::EVENT_RESPONSE,function(\Zend\View\ViewEvent $oEvent) use($oCallback){
			$oCallback($oEvent->getResult());
		});
		$oMessageView->render($oRenderer->layout());
		return $this;
	}

	/**
	 * Retrieve media renderer
	 * @param string $sMedia
	 * @throws \LogicException
	 * @throws \DomainException
	 * @return \Zend\View\Renderer\RendererInterface
	 */
	private function getRenderer($sMedia){
		if(isset($this->renderers[$sMedia]) && $this->renderers[$sMedia] instanceof \Zend\View\Renderer\RendererInterface)return $this->renderers[$sMedia];
		if(!isset($this->configuration['view_manager']['template_map'])
		|| !is_array($this->configuration['view_manager']['template_map']))throw new \LogicException('Messenger Service configuration is not valid : '.print_r($this->configuration['view_manager'],true));
		switch($sMedia){
			//Renderer for single view
			case 'default':
				$this->renderers[$sMedia] = new \Zend\View\Renderer\PhpRenderer();
				$this->renderers[$sMedia]->setResolver(new \Zend\View\Resolver\TemplateMapResolver($this->configuration['view_manager']['template_map']));
				break;

			//Renderer for email
			case self::MEDIA_EMAIL:
				$this->renderers[$sMedia] = new \BoilerAppMessenger\View\Renderer\EmailRenderer();
				$oLayout = new \Zend\View\Model\ViewModel();
				$this->renderers[$sMedia]->setResolver(new \Zend\View\Resolver\TemplateMapResolver($this->configuration['view_manager']['template_map']))
				->plugin('view_model')->setRoot($oLayout->setTemplate('email/layout'));

				//Footer view
				$oVueFooter = new \Zend\View\Model\ViewModel();
				$this->renderers[$sMedia]->layout()->addChild($oVueFooter->setTemplate('email/footer'),'footer');
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
	 * @param \AssetsBundle\Service\Service
	 * @return \Messenger\Service\MessengerService
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
	 * @throws \Exception
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	private function setTransporter(\Zend\Mail\Transport\TransportInterface $oTransporter,$sMedia){
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
	 * @throws \LogicException
	 * @return \Zend\Mail\Transport\TransportInterface
	 */
	private function getTransporter($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->transporters[$sMedia]) && $this->transporters[$sMedia] instanceof \Zend\Mail\Transport\TransportInterface)return $this->transporters[$sMedia];
		else throw new \LogicException('Transporter si not defined for media "'.$sMedia.'"');
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

	/**
	 * Inject a SharedEventManager instance
	 * @param \Zend\EventManager\SharedEventManagerInterface $oSharedEventManager
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function setSharedManager(\Zend\EventManager\SharedEventManagerInterface $oSharedEventManager){
		$this->sharedEventManager = $oSharedEventManager;
		return $this;
	}

	/**
	 * Get shared collections container
	 * @return \Zend\EventManager\SharedEventManagerInterface
	 */
	public function getSharedManager(){
		return $this->sharedEventManager instanceof \Zend\EventManager\SharedEventManagerInterface
		?$this->sharedEventManager
		:$this->sharedEventManager = \Zend\EventManager\StaticEventManager::getInstance();
	}

	/**
	 * Remove any shared collections
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function unsetSharedManager(){
		$this->sharedEventManager = null;
		return $this;
	}
}