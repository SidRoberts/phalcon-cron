<?php

namespace Sid\Phalcon\Cron\Job;

class Phalcon extends \Sid\Phalcon\Cron\Job
{
	/**
	 * @var array|null
	 */
	protected $body;



	/**
	 * @param string     $expression
	 * @param array|null $body
	 *
	 * @throws Exception
	 */
	public function __construct($expression, $body = null)
	{
		$di = $this->getDI();
		if (!($di instanceof \Phalcon\DiInterface)) {
			throw new Exception("A dependency injection object is required to access internal services");
		}
		
		
		
		parent::__construct($expression);
		
		
		
		$this->body = $body;
	}
	
	
	
	/**
	 * @return array|null
	 */
	public function getBody()
	{
		return $this->body;
	}
	
	
	
	/**
	 * @return string
	 */
	public function runInForeground()
	{
		ob_start();
		
		$this->getDI()->get("console")->handle(
			$this->getBody()
		);
		
		$contents = ob_get_contents();
		
		ob_end_clean();
		
		return $contents;
	}
}