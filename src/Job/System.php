<?php

namespace Sid\Phalcon\Cron\Job;

class System extends \Sid\Phalcon\Cron\Job
{
	/**
	 * @var string
	 */
	protected $command;
	
	/**
	 * @var string
	 */
	protected $output;
	
	
	
	/**
	 * @param string $expression
	 * @param string $command
	 * @param string $output
	 */
	public function __construct($expression, $command, $output = null)
	{
		parent::__construct($expression);
		
		$this->command = $command;
		$this->output  = $output;
	}
	
	
	
	/**
	 * @return string
	 */
	public function getCommand()
	{
		return $this->command;
	}
	
	/**
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}
	
	
	
	/**
	 * @return string
	 */
	private function buildCommand()
	{
		$command = $this->getCommand();
		
		if ($this->getOutput()) {
			$command .= ' > ' . $this->getOutput() . ' 2>&1';
		}
		
		return $command;
	}
	
	
	
	/**
	 * @return string
	 */
	public function runInForeground()
	{
		return shell_exec($this->buildCommand());
	}
}