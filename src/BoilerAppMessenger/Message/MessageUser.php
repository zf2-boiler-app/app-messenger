<?php
namespace BoilerAppMessenger\Message;
class MessageUser implements \BoilerAppMessenger\Media\Mail\MailMessageUserInterface{

	/**
	 * @var string
	 */
	protected $userDisplayName;

	/**
	 * @var string
	 */
	protected $userEmail;

	/**
	 * @param string $sUserDisplayName
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message\MessageUser
	 */
	public function setUserDisplayName($sUserDisplayName){
		if(is_string($sUserDisplayName))$this->userDisplayName = $sUserDisplayName;
		else throw new \InvalidArgumentException('User display name expects a string, "'.gettype($sUserDisplayName).'" given');
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserDisplayName(){
		if(is_string($this->userDisplayName))return $this->userDisplayName;
		throw new \LogicException('User display name is undefined');
	}

	/**
	 * @param string $sUserEmail
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message\MessageUser
	 */
	public function setUserEmail($sUserEmail){
		if(is_string($sUserEmail) && ($sEmail = filter_var($sUserEmail,FILTER_VALIDATE_EMAIL)))$this->userEmail = $sEmail;
		else throw new \InvalidArgumentException(sprintf(
			'User email expects a valid email adress, "%s" given',
			is_scalar($sUserEmail)
			?$sUserEmail
			:(is_object($sUserEmail)?get_class($sUserEmail):gettype($sUserEmail))
		));
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserEmail(){
		if(is_string($this->userEmail))return $this->userEmail;
		throw new \LogicException('User email is undefined');
	}
}