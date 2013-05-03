<?php
namespace BoilerAppMessenger\Factory;
class MailMessageRendererFactory implements \Zend\ServiceManager\FactoryInterface{
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oMailMessageRenderer = new \BoilerAppMessenger\Mail\MailMessageRenderer();

		//Create layout template
		$oLayout = new \Zend\View\Model\ViewModel();
		$oEvent = new \Zend\Mvc\MvcEvent(\Zend\Mvc\MvcEvent::EVENT_RENDER);
		$this->getTemplatingService()->buildLayoutTemplate($oEvent->setRequest(new \Zend\Http\Request())->setViewModel($oLayout));

		$oMailMessageRenderer->setResolver(
			new \Zend\View\Resolver\TemplateMapResolver($this->getOptions()->hasTemplateMap()?$this->getOptions()->getTemplateMap():null)
		)->plugin('view_model')->setRoot($oEvent->getViewModel());

		//Add mandatory helpers
		$oTranslateHelper = new \Zend\I18n\View\Helper\Translate();
		$oMailMessageRenderer->getHelperPluginManager()->setService(
			'translate',
			$oTranslateHelper->setTranslator($oServiceLocator->get('translator'))->setTranslatorEnabled(true)
		);

		$oUrlHelper = new \Zend\View\Helper\Url();
		$this->renderers[$sMedia]->getHelperPluginManager()->setService(
			'url',
			$oUrlHelper->setRouter($this->getRouter())
		);
	}
}