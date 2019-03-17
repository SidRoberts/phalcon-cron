<?php

namespace Sid\Phalcon\Cron\Tests\Job;

use Task\TaskTask;

class PhalconTest extends \Codeception\TestCase\Test
{
    protected function getDi()
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







    // tests
    public function testGetters()
    {
        $di = $this->getDi();

        $cron = $di->get("cron");



        $jobs = $cron->getDueJobs();

        $this->assertCount(
            1,
            $jobs
        );

        $job = $jobs[0];

        $this->assertEquals(
            "* * * * *",
            $job->getExpression()
        );

        $this->assertEquals(
            $job->getBody(),
            [
                "task"   => TaskTask::class,
                "action" => "action",
                "params" => [
                    "param1",
                    "param2",
                    "param3",
                ]
            ]
        );
    }



    public function testRunningInForeground()
    {
        $di = $this->getDi();

        $cron = $di->get("cron");



        $outputs = $cron->runInForeground();

        $this->assertEquals(
            print_r(
                [
                    "param1",
                    "param2",
                    "param3",
                ],
                true
            ),
            $outputs[0]
        );
    }
}
