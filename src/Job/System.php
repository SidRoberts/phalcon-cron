<?php

namespace Sid\Phalcon\Cron\Job;

use Sid\Phalcon\Cron\Job;

class System extends Job
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $output;



    public function __construct(string $expression, string $command, string $output = null)
    {
        parent::__construct($expression);

        $this->command = $command;
        $this->output  = $output;
    }



    public function getCommand() : string
    {
        return $this->command;
    }

    /**
     * @return string|null
     */
    public function getOutput()
    {
        return $this->output;
    }



    private function buildCommand() : string
    {
        $command = $this->getCommand();
        $output  = $this->getOutput();

        if ($output) {
            $command .= " > " . $output . " 2>&1";
        }

        return $command;
    }



    /**
     * @return string|null
     */
    public function runInForeground()
    {
        return shell_exec(
            $this->buildCommand()
        );
    }
}
