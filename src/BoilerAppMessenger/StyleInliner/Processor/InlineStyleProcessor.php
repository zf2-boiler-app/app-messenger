<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
class InlineStyleProcessor extends \BoilerAppMessenger\StyleInliner\Processor\AbstractProcessor{

	/**
	 * @var \InlineStyle\InlineStyle
	 */
	protected $inlineStyle;

	/**
	 * @param string $sHtml
	 * @throws \InvalidArgumentException
	 */
	public function process($sHtml){
		if(is_string($sHtml)){
			$oInlineStyle = $this->getInlineStyle();
			$oInlineStyle->loadHTML($sHtml);
			return $oInlineStyle->applyStylesheet($oInlineStyle->extractStylesheets(null,$this->getBaseDir()))->getHTML();
		}
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');
	}

	/**
	 * @throws \LogicException
	 * @return \InlineStyle\InlineStyle
	 */
	private function getInlineStyle(){
		if($this->inlineStyle instanceof \InlineStyle\InlineStyle)return $this->inlineStyle;
		elseif(class_exists('\InlineStyle\InlineStyle'))return $this->inlineStyle = new \InlineStyle\InlineStyle();
		else throw new \LogicException('\InlineStyle\InlineStyle class is undefined');
	}
}