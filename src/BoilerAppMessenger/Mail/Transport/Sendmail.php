<?php
namespace BoilerAppMessenger\Mail\Transport;
class Sendmail extends \Zend\Mail\Transport\Sendmail{
	use \BoilerAppMessenger\Mail\Transport\AttachementsAwareTrait;
}