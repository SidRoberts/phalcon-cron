<?php

namespace Tests\Unit;

use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\Di;
use Phalcon\Di\DiInterface;
use Sid\Phalcon\Cron\Job\Phalcon as PhalconJob;
use Sid\Phalcon\Cron\Job\System as SystemJob;
use Sid\Phalcon\Cron\Manager;
use Tests\Support\Task\TaskTask;
use Tests\Support\UnitTester;

class ManagerCest
{
    protected function getDi(): DiInterface
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
                            ],
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



    public function addJobsToCron(UnitTester $I): void
    {
        Di::reset();

        $di = $this->getDi();



        $cron = new Manager();

        $cronJob1 = new PhalconJob("* * * * *", "task", "action", "params");
        $cronJob2 = new SystemJob("* * * * *", "echo 'hello world'");

        $I->assertCount(
            0,
            $cron->getDueJobs()
        );

        $cron->add($cronJob1);

        $I->assertCount(
            1,
            $cron->getDueJobs()
        );

        $cron->add($cronJob2);

        $I->assertCount(
            2,
            $cron->getDueJobs()
        );
    }



    public function addJobsFromCrontab(UnitTester $I): void
    {
        $cron = new Manager();

        $cron->addCrontab(
            codecept_data_dir() . "/crontabs/crontab2"
        );

        $jobs = $cron->getAllJobs();



        $I->assertCount(
            2,
            $jobs
        );



        $I->assertEquals(
            "@hourly",
            $jobs[0]->getExpression()
        );

        $I->assertEquals(
            "sh purge-cache.sh",
            $jobs[0]->getCommand()
        );



        $I->assertEquals(
            "* 0 * * *",
            $jobs[1]->getExpression()
        );

        $I->assertEquals(
            "sh backup.sh",
            $jobs[1]->getCommand()
        );
    }
}
