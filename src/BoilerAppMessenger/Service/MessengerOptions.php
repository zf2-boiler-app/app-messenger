<?php
namespace BoilerAppMessenger\Service;
class MessengerOptions extends \Zend\Stdlib\AbstractOptions{
	/**
	 * @var array
	 */
	protected $systemUser;

	/**
	 * @var array
	 */
	protected $viewManager;

	/**
	 * @param array $aSystemUser
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerOptions
	 */
	public function setSystemUser(array $aSystemUser){
		if(!isset($aSystemUser['email'],$aSystemUser['name']))throw new \InvalidArgumentException('system user expects "email" and "name" keys, "'.join(', ',array_keys($aSystemUser)).'" given');
		if(!($sEmail = filter_var($aSystemUser['email'],FILTER_VALIDATE_EMAIL)))throw new \InvalidArgumentException(sprintf(
			'system user email expects valid email adress, "%s" given',
			is_scalar($sEmail)?$sEmail:(is_object($sEmail)?get_class($sEmail):gettype($sEmail))
		));
		if(!is_string($aSystemUser['name']))throw new \InvalidArgumentException('system user email expects string, "'.gettype($aSystemUser['name']).'" given');
		$this->systemUser = $aSystemUser;
		return $this;
	}


	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getSystemUserEmail(){
		if(!isset($this->systemUser['email']))throw new \LogicException('System user email is undefined');
		return $this->systemUser['email'];
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getSystemUserName(){
		if(!isset($this->systemUser['name']))throw new \LogicException('System user name is undefined');
		return $this->systemUser['name'];
	}

	/**
	 * @param array $aViewManager
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Service\MessengerOptions
	 */
	public function setViewManager(array $aViewManager){
		if(isset($aViewManager['template_map']) && !is_array($aViewManager['template_map']))throw new \InvalidArgumentException('Template map expects array, "'.gettype($aViewManager['template_map']).' given');
		$this->viewManager = $aViewManager;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return array
	 */
	public function getTemplateMap(){
		if(!$this->hasTemplateMap())throw new \LogicException('Template map is undefined');
		return $this->viewManager['template_map'];
	}

	/**
	 * @return boolean
	 */
	public function hasTemplateMap(){
		return isset($this->viewManager['template_map']);
	}
}