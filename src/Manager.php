<?php

namespace Sid\Phalcon\Cron;

class Manager
{
	/**
	 * @var array
	 */
	protected $jobs = [];
	
	/**
	 * For background jobs.
	 * 
	 * @var array
	 */
	protected $processes = [];
	
	
	
	/**
	 * @param Job $job
	 */
	public function add(Job $job)
	{
		$this->jobs[] = $job;
	}
	
	/**
	 * @param string $filename
	 */
	public function addCrontab($filename)
	{
		$crontab = new CrontabParser($filename);
		
		foreach ($crontab->getJobs() as $job) {
			$this->add($job);
		}
	}
	
	
	
	/**
	 * @param \DateTime|string $now
	 * 
	 * @return array
	 */
	public function getDueJobs($now = null)
	{
		$jobs = $this->jobs;
		
		$jobs = array_filter(
			$jobs,
			function ($job) use ($now) {
				return $job->isDue($now);
			}
		);
		
		return $jobs;
	}
	
	
	
	/**
	 * @return array
	 */
	public function getAllJobs()
	{
		return $this->jobs;
	}
	
	
	
	/**
	 * Run all due jobs in the foreground.
	 * 
	 * @param \DateTime|string $now
	 * 
	 * @return array
	 */
	public function runInForeground($now = null)
	{
		$jobs = $this->getDueJobs($now);
		
		$outputs = [];
		
		foreach ($jobs as $job) {
			$outputs[] = $job->runInForeground();
		}
		
		return $outputs;
	}
	
	/**
	 * Run all due jobs in the background.
	 * 
	 * @param \DateTime|string $now
	 * 
	 * @return array
	 */
	public function runInBackground($now = null)
	{
		$jobs = $this->getDueJobs($now);
		
		foreach ($jobs as $job) {
			$this->processes[] = $job->runInBackground();
		}
		
		return $this->processes;
	}
	
	
	
	/**
	 * Wait for all jobs running in the background to finish.
	 */
	public function wait()
	{
		foreach ($this->processes as $process) {
			$process->wait();
		}
	}
	
	
	
	/**
	 * Terminate all jobs running in the background.
	 */
	public function terminate()
	{
		foreach ($this->processes as $process) {
			$process->terminate();
		}
	}
	
	/**
	 * Kill all jobs running in the background.
	 */
	public function kill()
	{
		foreach ($this->processes as $process) {
			$process->kill();
		}
	}
}