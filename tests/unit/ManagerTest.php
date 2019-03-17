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

        $this->assertEquals(
            0,
            count($cron->getDueJobs())
        );

        $cron->add($cronJob1);

        $this->assertEquals(
            1,
            count($cron->getDueJobs())
        );

        $cron->add($cronJob2);

        $this->assertEquals(
            2,
            count($cron->getDueJobs())
        );
    }



    public function testAddJobsFromCrontab()
    {
        $cron = new Manager();

        $cron->addCrontab(
            __DIR__ . "/crontabs/crontab2"
        );

        $jobs = $cron->getAllJobs();



        $this->assertEquals(
            2,
            count($jobs)
        );



        $this->assertEquals(
            "@hourly",
            $jobs[0]->getExpression()
        );

        $this->assertEquals(
            "sh purge-cache.sh",
            $jobs[0]->getCommand()
        );



        $this->assertEquals(
            "* 0 * * *",
            $jobs[1]->getExpression()
        );

        $this->assertEquals(
            "sh backup.sh",
            $jobs[1]->getCommand()
        );
    }
}
