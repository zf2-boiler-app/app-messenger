<?php
namespace BoilerAppMessenger\StyleInliner;
class StyleInlinerService{
	/**
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerOptions
	 */
	protected $options;

	/**
	 * @var \InlineStyle\InlineStyle
	 */
	protected $inlineStyle;

	public function __construct(\BoilerAppMessenger\StyleInliner\StyleInlinerOptions $oOptions = null){
		if($oOptions)$this->setOptions($oOptions);
	}

	/**
	 * Set configuration parameters for InlineStyle
	 * @param  array|Traversable $aOptions
	 * @return \Messenger\Mail\InlineStyle
	 * @throws \Exception
	 */
	public function setOptions(\BoilerAppMessenger\StyleInliner\StyleInlinerOptions $oOptions){
		$this->options = $oOptions;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\StyleInliner\StyleInlinerOptions
	 */
	public function getOptions(){
		if($this->options instanceof \BoilerAppMessenger\StyleInliner\StyleInlinerOptions)return $this->options;
		throw new \LogicException('StyleInliner options are undefined');
	}

	/**
	 * @param string $sHtml
	 * @throws \Exception
	 * @return string
	 */
	public function processHtml($sHtml){
		if(is_string($sHtml))return $this->getOptions()->getProcessor()->process($sHtml);
		throw new \InvalidArgumentException('Html expects string, "'.gettype($sHtml).'" given');

	}
}