<?php

namespace Sid\Phalcon\Cron\Tests\Job;

class CallbackTest extends \Codeception\TestCase\Test
{
    public function testRunInForeground()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\Callback(
            "* * * * *",
            function () {
                return "hello world";
            }
        );

        $output = $cronJob->runInForeground();

        $this->assertTrue(
            is_callable(
                $cronJob->getCallback()
            )
        );

        $this->assertEquals(
            $output,
            "hello world"
        );
    }



    public function testCallbackCronJobsInBackground()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\Callback(
            "* * * * *",
            function () {
                sleep(1);
            }
        );

        $process = $cronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->wait();

        $this->assertFalse(
            $process->isRunning()
        );
    }



    public function testTerminateBackgroundCronJob()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\Callback(
            "* * * * *",
            function () {
                sleep(2);
            }
        );

        $process = $cronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->terminate();

        $this->assertFalse(
            $process->isRunning()
        );
    }

    public function testKillBackgroundCronJob()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\Callback(
            "* * * * *",
            function () {
                sleep(2);
            }
        );

        $process = $cronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->kill();

        $this->assertFalse(
            $process->isRunning()
        );
    }
}
