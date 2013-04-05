<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
interface StyleInlinerProcessorInterface{

	/**
	 * @param string $shtml
	 * @return string
	 */
	public function process($sHtml);

	/**
	 * @param string $sServerUrl
	 */
	public function setServerUrl($sServerUrl);

	/**
	 * @return string
	 */
	public function getServerUrl();
}