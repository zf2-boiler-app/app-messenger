<?php
namespace BoilerAppMessenger\Message;
interface MessageUserInterface{
	/**
	 * @return string
	 */
	public function getUserDisplayName();
}