<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
class CssToInlineStylesProcessor extends \BoilerAppMessenger\StyleInliner\Processor\AbstractProcessor{

	/**
	 * @var string
	 */
	protected $encoding = 'UTF-8';

	/**
	 * @var \DOMDocument
	 */
	protected $domDocument;

	/**
	 * @var string
	 */
	protected $css;

	/**
	 * @var \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles
	 */
	protected $cssToInlineStyles;

	/**
	 * @param string $sHtml
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function process($sHtml){
		if(is_string($sHtml)){
			$oCssToInlineStyles = $this->getCssToInlineStyles();
			$this->setHtml($sHtml);
			$oCssToInlineStyles->setHTML($this->getHtml());
			$oCssToInlineStyles->setCSS($this->getCss());
			return $oCssToInlineStyles->convert();
		}
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');
	}

	/**
	 * @param string $sEncoding
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor
	 */
	public function setEncoding($sEncoding){
		if(!is_string($sEncoding))throw new \InvalidArgumentException('Encoding expects string, "'.gettype($sEncoding).'" given');
		$this->encoding = $sEncoding;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEncoding(){
		return $this->encoding;
	}

	/**
	 * @return \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles
	 */
	private function getCssToInlineStyles(){
		if($this->cssToInlineStyles instanceof \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles)return$this->cssToInlineStyles;
		$oCssToInlineStyles = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
		$oCssToInlineStyles->setEncoding($this->getEncoding());
		return $oCssToInlineStyles;
	}

	/**
	 * @param string $sHtml
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor
	 */
	private function setHtml($sHtml){
		if(is_string($sHtml)){
			$this->domDocument = new \DOMDocument('1.0', $this->getEncoding());
			$this->domDocument->loadHTML(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $sHtml));
			$this->css = '';
			return $this->extractCss(null,$this->getBaseDir());
		}
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');
	}

	/**
	 * @param \DOMNode $oNode
	 * @param string $sBase
	 * @throws \LogicException
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor
	 */
	private function extractCss(\DOMNode $oNode = null, $sBase = ''){
		if(null === $oNode) {
			if($this->domDocument instanceof \DOMDocument)$oNode = $this->domDocument;
			else throw new \LogicException('Dom document is undefined');
		}

		if(strtolower($oNode->nodeName) === 'style'){
			$this->css .= $oNode->nodeValue.PHP_EOL;
			$oNode->parentNode->removeChild($oNode);
		}
		elseif(strtolower($oNode->nodeName) === 'link'){
			if($oNode->hasAttribute('href')) {
				$sHref = $oNode->getAttribute('href');

				if($sBase && false === strpos($sHref, '://'))$sHref = $sBase.'/'.$sHref;

				//href is not an url : remove query part if exists
				if(false === strpos($sHref, "://") && false !== strpos($sHref, '?'))$sHref = current(explode('?',$sHref));

				if(($sContent = @file_get_contents($sHref)) !== false)$this->css .= $sContent.PHP_EOL;
				$oNode->parentNode->removeChild($oNode);
			}
		}

		if($oNode->hasChildNodes()){
			$aChildNodes = array();
			for($iIterator = 0; $iIterator < $oNode->childNodes->length; ++$iIterator){
				$aChildNodes[] = $oNode->childNodes->item($iIterator);
			}
			foreach($aChildNodes as $oChild){
				$this->extractCss($oChild, $sBase);
			}
		}
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	private function getHtml(){
		if($this->domDocument instanceof \DOMDocument)return $this->domDocument->saveHTML();
		throw new \LogicException('Dom document is undefined');
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	private function getCss(){
		if(is_string($this->css))return $this->css;
		throw new \LogicException('Css is undefined');
	}
}