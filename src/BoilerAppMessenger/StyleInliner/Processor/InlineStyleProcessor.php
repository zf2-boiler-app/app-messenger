<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
class InlineStyleProcessor extends \BoilerAppMessenger\StyleInliner\Processor\AbstractProcessor{

	/**
	 * @var \InlineStyle\InlineStyle
	 */
	protected $inlineStyle;

	/**
	 * @param string $sHtml
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function process($sHtml){
		if(is_string($sHtml)){
			$oInlineStyle = $this->getInlineStyle();

			//Remove query part from link url if base dir is not an url
			if(strpos($this->getBaseDir(), '://') === false){
				$oDOMDocument = new \DOMDocument();
				$oDOMDocument->loadHTML($sHtml);
				$oDOMXPath = new \DOMXPath($oDOMDocument);
				foreach($oDOMXPath->query('//*/link[@rel="stylesheet"][contains(@href,\'?\')]/@href') as $oLinkNode){
					if(strpos($oLinkNode->nodeValue, '://') === false)$oLinkNode->nodeValue = current(explode('?',$oLinkNode->nodeValue));
				}
				$sHtml = $oDOMDocument->saveHTML();
			}
			$oInlineStyle->loadHTML($sHtml);
			return $oInlineStyle->applyStylesheet($oInlineStyle->extractStylesheets(null,$this->getBaseDir()))->getHTML();
		}
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');
	}

	/**
	 * @return \InlineStyle\InlineStyle
	 */
	private function getInlineStyle(){
		return $this->inlineStyle instanceof \InlineStyle\InlineStyle?$this->inlineStyle:$this->inlineStyle = new \InlineStyle\InlineStyle();
	}
}