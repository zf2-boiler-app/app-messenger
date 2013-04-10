<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
interface StyleInlinerProcessorInterface{

	/**
	 * @param string $shtml
	 * @return string
	 */
	public function process($sHtml);

	/**
	 * @param string $sBaseDir
	 */
	public function setbaseDir($sBaseDir);

	/**
	 * @return string
	 */
	public function getBaseDir();
}