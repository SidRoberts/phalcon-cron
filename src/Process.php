<?php

namespace Sid\Phalcon\Cron;

/**
 * As this class uses PNCTL/POSIX functions and applies a shutdown handler that
 * waits for the process to finish, you should not use this class to interact
 * with regular processes.
 */
class Process
{
	/**
	 * @var int
	 */
	protected $processID;
	
	
	
	/**
	 * @param int $processID
	 */
	public function __construct($processID)
	{
		$this->processID = $processID;
		
		
		
		register_shutdown_function([$this, "wait"]);
	}
	
	
	
	/**
	 * @return int
	 */
	public function getProcessID()
	{
		return $this->processID;
	}
	
	
	
	/**
	 * Determine if this process is currently running. Defunct/zombie processes
	 * are ignored.
	 * 
	 * @return boolean
	 */
	public function isRunning()
	{
		$result = shell_exec(sprintf("ps %d", $this->getProcessID()) . " | grep -v '<defunct>'");
		
		return (count(preg_split("/\n/", $result)) > 2);
	}
	
	
	
	/**
	 * Wait for the process to finish.
	 */
	public function wait()
	{
		pcntl_waitpid($this->getProcessID(), $status);
	}
	
	
	
	/**
	 * Terminate the process.
	 * 
	 * @return boolean
	 */
	public function terminate()
	{
		return posix_kill($this->getProcessID(), SIGTERM);
	}
	
	/**
	 * Kill the process.
	 * 
	 * @return boolean
	 */
	public function kill()
	{
		return posix_kill($this->getProcessID(), SIGKILL);
	}
}