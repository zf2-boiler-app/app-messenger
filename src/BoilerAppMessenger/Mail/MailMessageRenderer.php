<?php
namespace BoilerAppMessenger\Mail;
class MailMessageRenderer extends \Zend\View\Renderer\PhpRenderer implements \BoilerAppMessenger\MessageRendererInterface{

	/**
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	private $styleInliner;

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
	protected function getStyleInliner(){
		if($this->styleInliner instanceof \BoilerAppMessenger\StyleInliner\StyleInlinerService)return $this->styleInliner;
		throw new \LogicException('StyleInliner is undefined');
	}

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @param callable $oCallback
	 * @return \BoilerAppMessenger\MessageRendererInterface
	 */
	public function renderMessage(\BoilerAppMessenger\Message $oMessage,callable $oCallback){
		$this->layout()->subject = $oMessage->getSubject();
		$this->layout()->content = $oMessage->getBody();

		//Render html
		$this->getStyleInliner()->processHtml();

		call_user_func($oCallback,$oMessage);
	}
}