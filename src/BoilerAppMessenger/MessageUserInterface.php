<?php
namespace BoilerAppMessenger;
interface MessageUserInterface{
	/**
	 * @param string $sName
	 * @return \BoilerAppMessenger\MessageUserInterface
	 */
	public function setUserDisplayName($sName);

	/**
	 * @return string
	 */
	public function getUserDisplayName();
}