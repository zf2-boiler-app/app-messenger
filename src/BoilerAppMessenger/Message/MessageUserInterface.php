<?php
namespace BoilerAppMessenger\Message;
interface MessageUserInterface{
	/**
	 * @param string $sUserDisplayName
	 * @return \BoilerAppMessenger\Message\MessageUserInterface
	 */
	public function setUserDisplayName($sUserDisplayName);

	/**
	 * @return string
	 */
	public function getUserDisplayName();
}