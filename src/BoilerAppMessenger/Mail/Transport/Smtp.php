<?php
namespace BoilerAppMessenger\Mail\Transport;
class Smtp extends \Zend\Mail\Transport\Smtp{
	use \BoilerAppMessenger\Mail\Transport\AttachementsAwareTrait;
}