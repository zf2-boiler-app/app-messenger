<?php
namespace BoilerAppMessenger\Factory;
class MailMessageRendererFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oMailMessageRenderer = new \BoilerAppMessenger\Media\Mail\MailMessageRenderer();

		//Template map
		$aConfiguration = $oServiceLocator->get('Config');
		if(isset($aConfiguration['medias'][\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA])){
			$aConfiguration = $aConfiguration['medias'][\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA];
			if(isset($aConfiguration['template_map']))$oMailMessageRenderer->setTemplateMap($aConfiguration['template_map']);

			//Templating service
			if(class_exists('TreeLayoutStack\\TemplatingService'))$oMailMessageRenderer->setTemplatingService(\TreeLayoutStack\TemplatingService::factory(
		       	isset($aConfiguration['tree_layout_stack'])?$aConfiguration['tree_layout_stack']:array()
			));
		}

		//AssetsBundle service
		if($oServiceLocator->has('AssetsBundleService'))$oMailMessageRenderer->setAssetsBundleService($oServiceLocator->get('AssetsBundleService'));

		//StyleInliner service
		if($oServiceLocator->has('StyleInlinerService'))$oMailMessageRenderer->setStyleInlinerService($oServiceLocator->get('StyleInlinerService'));

		return $oMailMessageRenderer;
	}
}