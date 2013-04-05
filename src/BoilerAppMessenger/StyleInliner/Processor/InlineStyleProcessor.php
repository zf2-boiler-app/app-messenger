<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
class InlineStyleProcessor implements \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface{
	/**
	 * @var \InlineStyle\InlineStyle
	 */
	protected $inlineStyle;

	/**
	 * @var string
	 */
	protected $serverUrl;

	/**
	 *
	 * @param unknown $sHtml
	 * @throws \InvalidArgumentException
	 */
	public function process($sHtml){
		if(is_string($sHtml))return $this->getInlineStyle($sHtml)->getHTML();
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');
	}

	/**
	 * @param string $sHtml
	 * @throws \LogicException
	 * @return \InlineStyle\InlineStyle
	 */
	private function getInlineStyle($sHtml = null){
		if($this->inlineStyle instanceof \InlineStyle\InlineStyle)$this->inlineStyle->loadHTML($sHtml);
		elseif(class_exists('\InlineStyle\InlineStyle'))$this->inlineStyle = new \InlineStyle\InlineStyle($sHtml);
		else throw new \LogicException('\InlineStyle\InlineStyle class is undefined');
		return $this->inlineStyle->applyStylesheet($this->inlineStyle->extractStylesheets(null,$this->options->getServerUrl()));
	}

	/**
	 * @param string $sServerUrl
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\StyleInliner\StyleInlinerOptions
	 */
	public function setServerUrl($sServerUrl){
		if(filter_var($sServerUrl,FILTER_VALIDATE_URL) === false)throw new \InvalidArgumentException(sprintf(
			'Server url expects valid url, "%s" given',
			is_scalar($sServerUrl)?$sServerUrl:(is_object($sServerUrl)?get_class($sServerUrl):gettype($sServerUrl))
		));
		$this->serverUrl = $sServerUrl;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getServerUrl(){
		if(is_string($this->serverUrl))return $this->serverUrl;
		throw new \LogicException('Server Url option is undefined');
	}
}