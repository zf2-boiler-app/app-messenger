<?php
namespace BoilerAppMessenger\Media\Mail;
class MailMessageRenderer extends \Zend\View\Renderer\PhpRenderer implements \BoilerAppMessenger\Message\MessageRendererInterface{
	const MEDIA = 'mail';

	/**
	 * @var \TreeLayoutStack\TemplatingService
	 */
	protected $templatingService;

	/**
	 * @var \AssetsBundle\Service\Service
	 */
	protected $assetsBundleService;

	/**
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	protected $styleInlinerService;

	/**
	 * Init layout view model
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function initLayout(){
		//Create layout template
		$oEvent = new \Zend\Mvc\MvcEvent(\Zend\Mvc\MvcEvent::EVENT_RENDER);
		$oEvent->setViewModel(new \Zend\View\Model\ViewModel());

		if($this->hasTemplatingService())$this->getTemplatingService()->buildLayoutTemplate($oEvent->setRequest(new \Zend\Http\Request()));

		$this->plugin('view_model')->setRoot($oEvent->getViewModel());

		return $this;
	}

	/**
	 * @param \Zend\View\Model\ModelInterface $oViewModel
	 * @throws \DomainException
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	protected function renderChildren(\Zend\View\Model\ModelInterface $oViewModel){
        foreach($oViewModel as $oChild){
            if($oChild->terminate())throw new \DomainException('Inconsistent state; child view model is marked as terminal');
            $oChild->setOption('has_parent', true);
            $sResult = $this->renderChildren($oChild)->render($oChild);
            $oChild->setOption('has_parent', null);
            $sCapture = $oChild->captureTo();
            if(!empty($sCapture))$oViewModel->setVariable($sCapture,$oChild->isAppend()?$oViewModel->{$sCapture}.$sResult:$sResult);
        }
        return $this;
    }

	/**
	 * @see \BoilerAppMessenger\Message\MessageRendererInterface::renderMessageBody()
	 * @param \BoilerAppMessenger\Message\Message $oMessage
	 * @return string
	 */
	public function renderMessageBody(\BoilerAppMessenger\Message\Message $oMessage){
		//Build layout
		$this->initLayout();

		//Retrieve layout
		$oLayout = $this->layout();

		//Set subject to layout
		$oLayout->subject = $oMessage->getSubject();

		//Set content to layout
		$oLayout->content = $this->render($oMessage->getBody());

		//Manage assets if service is available
		if($this->hasAssetsBundleService()){
			$this->getAssetsBundleService()->getOptions()
			->setModuleName(current(explode('\\',__NAMESPACE__)))
			->setControllerName(self::MEDIA)
			->setRenderer($this);

			$this->getAssetsBundleService()->renderAssets();
		}

		//Render children layout if needed
		if($oLayout->hasChildren())$this->renderChildren($oLayout);

		//Render layout
		$sBody = $this->render($oLayout);

		//Inline style if service is available
		if($this->hasStyleInlinerService())$sBody = $this->getStyleInlinerService()->processHtml($sBody);

		return $sBody;
	}

	/**
	 * @param array $aTemplateMap
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function setTemplateMap(array $aTemplateMap = null){
		$this->setResolver(new \Zend\View\Resolver\TemplateMapResolver($aTemplateMap));
		return $this;
	}

	/**
	 * @param \TreeLayoutStack\TemplatingService $oTemplatingService
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function setTemplatingService(\TreeLayoutStack\TemplatingService $oTemplatingService){
		$this->templatingService = $oTemplatingService;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \TreeLayoutStack\TemplatingService
	 */
	public function getTemplatingService(){
		if($this->hasTemplatingService())return $this->templatingService;
		throw new \LogicException('Templating service is undefined');
	}

	/**
	 * @return boolean
	 */
	public function hasTemplatingService(){
		return $this->templatingService instanceof \TreeLayoutStack\TemplatingService;
	}

	/**
	 * @param \AssetsBundle\Service\Service $oAssetsBundleService
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function setAssetsBundleService(\AssetsBundle\Service\Service $oAssetsBundleService){
		$this->assetsBundleService = $oAssetsBundleService;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \AssetsBundle\Service\Service
	 */
	public function getAssetsBundleService(){
		if($this->hasAssetsBundleService())return $this->assetsBundleService;
		throw new \LogicException('AssetsBundle service is undefined');
	}

	/**
	 * @return boolean
	 */
	public function hasAssetsBundleService(){
		return $this->assetsBundleService instanceof \AssetsBundle\Service\Service;
	}

	/**
	 * @param \BoilerAppMessenger\StyleInliner\StyleInlinerService $oStyleInlinerService
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function setStyleInlinerService(\BoilerAppMessenger\StyleInliner\StyleInlinerService $oStyleInlinerService){
		$this->styleInlinerService = $oStyleInlinerService;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	public function getStyleInlinerService(){
		if($this->hasStyleInlinerService())return $this->styleInlinerService;
		throw new \LogicException('StyleInliner service is undefined');
	}

	/**
	 * @return boolean
	 */
	public function hasStyleInlinerService(){
		return $this->styleInlinerService instanceof \BoilerAppMessenger\StyleInliner\StyleInlinerService;
	}
}