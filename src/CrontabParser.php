<?php

namespace Sid\Phalcon\Cron;

class CrontabParser
{
	/**
	 * @var string
	 */
	protected $filename;


	/**
	 * @param string $filename
	 *
	 * @throws Exception
	 */
	public function __construct($filename)
	{
		if (!file_exists($filename)) {
			throw new Exception("Crontab file does not exist.");
		}
		
		$this->filename = $filename;
	}
	
	
	
	/**
	 * @return array
	 */
	public function getJobs()
	{
		$contents = file_get_contents($this->filename);
		
		$lines = explode(PHP_EOL, $contents);
		
		$jobs = [];
		
		foreach ($lines as $line) {
			if (preg_match("/^(\@\w+|[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+)\s+(.*)$/", $line, $matches)) {
				$jobs[] = new Job\System($matches[1], $matches[2]);
			}
		}
		
		return $jobs;
	}
}