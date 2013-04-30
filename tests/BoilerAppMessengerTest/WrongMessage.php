<?php
namespace BoilerAppMessengerTest;
class WrongMessage extends \BoilerAppMessenger\Message{
	/**
	 * Set "From" sender, allows wrong values for tests
	 * @param mixed $oFrom
	 * @return \BoilerAppMessengerTest\WrongMessage
	 */
	public function setFrom($oFrom = self::SYSTEM_USER){
		$this->from = $oFrom;
		return $this;
	}

	/**
	 * Add one or more recipients to the "To" recipients, allows wrong values for tests
	 * @param \mixed $aTo
	 * @return \BoilerAppMessengerTest\WrongMessage
	 */
	public function addTo($aTo){
		$this->to = (array)$aTo;
		return $this;
	}
}