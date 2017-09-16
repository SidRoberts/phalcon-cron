<?php

namespace Sid\Phalcon\Cron\Tests;

use Codeception\TestCase\Test;
use Sid\Phalcon\Cron\Manager;
use Sid\Phalcon\Cron\Job\Phalcon as PhalconJob;
use Sid\Phalcon\Cron\Job\System as SystemJob;
use Sid\Phalcon\Cron\Job\Callback as CallbackJob;

class ManagerTest extends Test
{
    public function testAddJobsToCron()
    {
        $cron = new Manager();

        $cronJob1 = new PhalconJob("* * * * *", "task", "action", "params");
        $cronJob2 = new SystemJob("* * * * *", "echo 'hello world'");

        $this->assertEquals(count($cron->getDueJobs()), 0);

        $cron->add($cronJob1);

        $this->assertEquals(count($cron->getDueJobs()), 1);

        $cron->add($cronJob2);

        $this->assertEquals(count($cron->getDueJobs()), 2);
    }



    public function testCronJobsInBackground()
    {
        $cron = new Manager();

        $systemCronJob = new SystemJob("* * * * *", "sleep 1", null, "/dev/null");

        $cron->add($systemCronJob);

        $processes = $cron->runInBackground();

        $this->assertTrue($processes[0]->isRunning());

        $cron->wait();

        $this->assertFalse($processes[0]->isRunning());
    }

    public function testTerminateBackgroundCronJobs()
    {
        $cron = new Manager();

        $systemCronJob = new SystemJob(
            "* * * * *",
            "sleep 2",
            null,
            "/dev/null"
        );

        $callbackCronJob = new CallbackJob(
            "* * * * *",
            function () {
                sleep(2);
            }
        );

        $cron->add($systemCronJob);
        $cron->add($callbackCronJob);

        $processes = $cron->runInBackground();

        $this->assertTrue($processes[0]->isRunning());
        $this->assertTrue($processes[1]->isRunning());

        $cron->terminate();

        $this->assertFalse($processes[0]->isRunning());
        $this->assertFalse($processes[1]->isRunning());
    }

    public function testKillBackgroundCronJobs()
    {
        $cron = new Manager();

        $systemCronJob = new SystemJob(
            "* * * * *",
            "sleep 2",
            null,
            "/dev/null"
        );

        $callbackCronJob = new CallbackJob(
            "* * * * *",
            function () {
                sleep(2);
            }
        );

        $cron->add($systemCronJob);
        $cron->add($callbackCronJob);

        $processes = $cron->runInBackground();

        $this->assertTrue($processes[0]->isRunning());
        $this->assertTrue($processes[1]->isRunning());

        $cron->kill();

        $this->assertFalse($processes[0]->isRunning());
        $this->assertFalse($processes[1]->isRunning());
    }

    public function testAddJobsFromCrontab()
    {
        $cron = new Manager();

        $cron->addCrontab(__DIR__ . "/crontabs/crontab2");

        $jobs = $cron->getAllJobs();

        $this->assertEquals(count($jobs), 2);

        $this->assertEquals($jobs[0]->getExpression(), "@hourly");
        $this->assertEquals($jobs[0]->getCommand(), "sh purge-cache.sh");

        $this->assertEquals($jobs[1]->getExpression(), "* 0 * * *");
        $this->assertEquals($jobs[1]->getCommand(), "sh backup.sh");
    }
}
