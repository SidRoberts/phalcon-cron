<?php

namespace Sid\Phalcon\Cron\Tests;

use Codeception\TestCase\Test;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Sid\Phalcon\Cron\Manager;
use Sid\Phalcon\Cron\Job\Phalcon as PhalconJob;
use Sid\Phalcon\Cron\Job\System as SystemJob;
use Sid\Phalcon\Cron\Job\Callback as CallbackJob;
use Task\TaskTask;

class ManagerTest extends Test
{
    protected function getDi()
    {
        $di = new \Phalcon\Di\FactoryDefault\Cli();

        $di->setShared(
            "cron",
            function () {
                $cron = new Manager();

                $cron->add(
                    new PhalconJob(
                        "* * * * *",
                        [
                            "task"   => TaskTask::class,
                            "action" => "action",
                            "params" => [
                                "param1",
                                "param2",
                                "param3",
                            ]
                        ]
                    )
                );

                return $cron;
            }
        );

        $di->set(
            "console",
            function () {
                $console = new Console();

                return $console;
            }
        );

        $di->set(
            "dispatcher",
            function () {
                $dispatcher = new Dispatcher();

                $dispatcher->setTaskSuffix("");

                return $dispatcher;
            }
        );

        return $di;
    }



    public function testAddJobsToCron()
    {
        \Phalcon\Di::reset();

        $di = $this->getDi();



        $cron = new Manager();

        $cronJob1 = new PhalconJob("* * * * *", "task", "action", "params");
        $cronJob2 = new SystemJob("* * * * *", "echo 'hello world'");

        $this->assertCount(
            0,
            $cron->getDueJobs()
        );

        $cron->add($cronJob1);

        $this->assertCount(
            1,
            $cron->getDueJobs()
        );

        $cron->add($cronJob2);

        $this->assertCount(
            2,
            $cron->getDueJobs()
        );
    }



    public function testAddJobsFromCrontab()
    {
        $cron = new Manager();

        $cron->addCrontab(
            __DIR__ . "/crontabs/crontab2"
        );

        $jobs = $cron->getAllJobs();



        $this->assertCount(
            2,
            $jobs
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
