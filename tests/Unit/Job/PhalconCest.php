<?php

namespace Tests\Unit\Job;

use Phalcon\Di\DiInterface;
use Tests\Support\Task\TaskTask;
use Tests\Support\UnitTester;

class PhalconCest
{
    protected function getDi(): DiInterface
    {
        $di = new \Phalcon\Di\FactoryDefault\Cli();

        $di->setShared(
            "cron",
            function () {
                $cron = new \Sid\Phalcon\Cron\Manager();

                $cron->add(
                    new \Sid\Phalcon\Cron\Job\Phalcon(
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
                $console = new \Phalcon\Cli\Console();

                return $console;
            }
        );

        $di->set(
            "dispatcher",
            function () {
                $dispatcher = new \Phalcon\Cli\Dispatcher();

                $dispatcher->setTaskSuffix("");

                return $dispatcher;
            }
        );

        return $di;
    }



    public function getters(UnitTester $I): void
    {
        $di = $this->getDi();

        $cron = $di->get("cron");



        $jobs = $cron->getDueJobs();

        $I->assertCount(
            1,
            $jobs
        );

        $job = $jobs[0];

        $I->assertEquals(
            "* * * * *",
            $job->getExpression()
        );

        $I->assertEquals(
            [
                "task"   => TaskTask::class,
                "action" => "action",
                "params" => [
                    "param1",
                    "param2",
                    "param3",
                ],
            ],
            $job->getBody()
        );
    }



    public function runningInForeground(UnitTester $I): void
    {
        $di = $this->getDi();

        $cron = $di->get("cron");



        $outputs = $cron->runInForeground();

        $I->assertEquals(
            "param1" . PHP_EOL . "param2" . PHP_EOL . "param3",
            $outputs[0]
        );
    }
}
