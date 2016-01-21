<?php

namespace Sid\Phalcon\Cron;

abstract class Job extends \Phalcon\Di\Injectable
{
	/**
	 * @var string
	 */
	protected $expression;
	
	
	
	/**
	 * @param string $expression
	 */
	public function __construct($expression)
	{
		$this->expression = $expression;
	}
	
	
	
	/**
	 * @return string
	 */
	public function getExpression()
	{
		return $this->expression;
	}
	
	
	
	/**
	 * @param \DateTime|string $datetime
	 * 
	 * @return boolean
	 */
	public function isDue($datetime = "now")
	{
		return \Cron\CronExpression::factory($this->getExpression())->isDue($datetime);
	}



	/**
	 * @return mixed
	 */
	abstract public function runInForeground();

	/**
	 * @return Process
	 *
	 * @throws Exception
	 */
	public function runInBackground()
	{
		$processID = pcntl_fork();
		
		if ($processID == -1) {
			throw new Exception("Failed to fork process.");
		}
		
		// This is the child process.
		if ($processID == 0) {
			// @codeCoverageIgnoreStart
			$this->runInForeground();
			
			exit(0);
			// @codeCoverageIgnoreEnd
		}
		
		$process = new Process($processID);
		
		return $process;
	}
}