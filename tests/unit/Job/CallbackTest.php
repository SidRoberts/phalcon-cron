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
            "hello world",
            $output
        );
    }
}
