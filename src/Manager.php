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



    public function add(Job $job)
    {
        $this->jobs[] = $job;
    }

    public function addCrontab(string $filename)
    {
        $crontab = new CrontabParser($filename);

        $jobs = $crontab->getJobs();

        foreach ($jobs as $job) {
            $this->add($job);
        }
    }



    /**
     * @param \DateTime|string $now
     */
    public function getDueJobs($now = null) : array
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



    public function getAllJobs() : array
    {
        return $this->jobs;
    }



    /**
     * Run all due jobs in the foreground.
     *
     * @param \DateTime|string $now
     */
    public function runInForeground($now = null) : array
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
     */
    public function runInBackground($now = null) : array
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
