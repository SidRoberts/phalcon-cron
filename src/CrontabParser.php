<?php

namespace Sid\Phalcon\Cron;

use Sid\Phalcon\Cron\Job\System as SystemJob;

class CrontabParser
{
    /**
     * @var string
     */
    protected $filename;



    /**
     * @throws Exception
     */
    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new Exception(
                "Crontab file does not exist."
            );
        }

        $this->filename = $filename;
    }



    public function getJobs() : array
    {
        $contents = file_get_contents($this->filename);

        $lines = explode(PHP_EOL, $contents);

        $jobs = [];

        foreach ($lines as $line) {
            $cronLineRegEx = "/^(\@\w+|[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+\s[^\s]+)\s+(.*)$/";

            if (preg_match($cronLineRegEx, $line, $matches)) {
                $jobs[] = new SystemJob(
                    $matches[1],
                    $matches[2]
                );
            }
        }

        return $jobs;
    }
}
