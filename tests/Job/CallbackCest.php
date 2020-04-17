<?php

namespace Tests\Job;

use Tests\UnitTester;

class CallbackCest
{
    public function runInForeground(UnitTester $I)
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\Callback(
            "* * * * *",
            function () {
                return "hello world";
            }
        );

        $output = $cronJob->runInForeground();



        $I->assertIsCallable(
            $cronJob->getCallback()
        );

        $I->assertEquals(
            "hello world",
            $output
        );
    }
}
