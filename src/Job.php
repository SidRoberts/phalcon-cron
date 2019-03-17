<?php

namespace Sid\Phalcon\Cron;

use Cron\CronExpression;
use Phalcon\Di\Injectable;

abstract class Job extends Injectable
{
    /**
     * @var string
     */
    protected $expression;



    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }



    public function getExpression() : string
    {
        return $this->expression;
    }



    /**
     * @param \DateTime|string $datetime
     */
    public function isDue($datetime = "now") : bool
    {
        return CronExpression::factory($this->getExpression())->isDue($datetime);
    }



    /**
     * @return mixed
     */
    abstract public function runInForeground();

    /**
     * @throws Exception
     */
    public function runInBackground() : Process
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
