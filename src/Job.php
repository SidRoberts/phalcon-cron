<?php

namespace Sid\Phalcon\Cron;

use Cron\CronExpression;
use DateTime;
use Phalcon\Di\Injectable;

abstract class Job extends Injectable implements \Sid\Cron\JobInterface
{
    protected string $expression;



    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }



    public function getExpression(): string
    {
        return $this->expression;
    }



    public function isDue(DateTime $datetime = null): bool
    {
        $cronExpression = new CronExpression(
            $this->getExpression()
        );

        if (!$datetime) {
            $datetime = new DateTime();
        }

        return $cronExpression->isDue($datetime);
    }



    /**
     * @return mixed
     */
    abstract public function runInForeground();

    /**
     * @throws Exception
     */
    public function runInBackground(): Process
    {
        $processID = pcntl_fork();

        if ($processID === -1) {
            throw new Exception(
                "Failed to fork process."
            );
        }

        // This is the child process.
        if ($processID === 0) {
            // @codeCoverageIgnoreStart
            $this->runInForeground();

            exit(0);
            // @codeCoverageIgnoreEnd
        }

        $process = new Process($processID);

        return $process;
    }
}
