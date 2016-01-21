<?php

namespace Sid\Phalcon\Cron\Job;

class Callback extends \Sid\Phalcon\Cron\Job
{
	/**
	 * @var callable
	 */
	protected $callback;
	
	
	
	/**
	 * @param string   $expression
	 * @param callable $callback
	 */
	public function __construct($expression, callable $callback)
	{
		parent::__construct($expression);
		
		$this->callback = $callback;
	}
	
	
	
	/**
	 * @return callable
	 */
	public function getCallback()
	{
		return $this->callback;
	}
	
	
	
	/**
	 * @return mixed
	 */
	public function runInForeground()
	{
		$contents = call_user_func($this->callback);
		
		return $contents;
	}
}