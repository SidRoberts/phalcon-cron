<?php

namespace Sid\Phalcon\Cron\Job;

use Sid\Phalcon\Cron\Exception as CronException;

class Phalcon extends \Sid\Phalcon\Cron\Job
{
	/**
	 * @var string|null
	 */
	protected $task;
	
	/**
	 * @var string|null
	 */
	protected $action;
	
	/**
	 * @var array|null
	 */
	protected $params;
	
	
	
	/**
	 * @param string      $expression
	 * @param string|null $task
	 * @param string|null $action
	 * @param array|null  $params
	 */
	public function __construct($expression, $task = null, $action = null, $params = null)
	{
		$di = $this->getDI();
		if (!($di instanceof \Phalcon\DiInterface)) {
			throw new CronException("A dependency injection object is required to access internal services");
		}
		
		
		
		parent::__construct($expression);
		
		
		
		$this->task   = $task;
		$this->action = $action;
		$this->params = $params;
	}
	
	
	
	/**
	 * @return string|null
	 */
	public function getTask()
	{
		return $this->task;
	}
	
	/**
	 * @return string|null
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * @return array|null
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	
	
	/**
	 * @return string
	 */
	public function runInForeground()
	{
		ob_start();
		
		$this->getDI()->get("console")->handle(
			[
				"task"   => $this->getTask(),
				"action" => $this->getAction(),
				"params" => $this->getParams()
			]
		);
		
		$contents = ob_get_contents();
		
		ob_end_clean();
		
		return $contents;
	}
}