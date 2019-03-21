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



    public function __construct(int $processID)
    {
        $this->processID = $processID;



        register_shutdown_function(
            [
                $this,
                "wait",
            ]
        );
    }



    public function getProcessID() : int
    {
        return $this->processID;
    }



    /**
     * Determine if this process is currently running. Defunct/zombie processes
     * are ignored.
     */
    public function isRunning() : bool
    {
        $result = shell_exec(
            sprintf(
                "ps -p %d --no-headers | grep -v '<defunct>'",
                $this->getProcessID()
            )
        );

        $result = trim($result, "\n");

        return ($result !== "");
    }



    /**
     * Wait for the process to finish.
     */
    public function wait()
    {
        pcntl_waitpid(
            $this->getProcessID(),
            $status
        );
    }



    /**
     * Terminate the process.
     */
    public function terminate() : bool
    {
        return posix_kill(
            $this->getProcessID(),
            SIGTERM
        );
    }

    /**
     * Kill the process.
     */
    public function kill() : bool
    {
        return posix_kill(
            $this->getProcessID(),
            SIGKILL
        );
    }
}
