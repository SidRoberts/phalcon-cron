<?php

namespace Tests\Unit\Job;

use Tests\Support\UnitTester;

class CallbackCest
{
    public function runInForeground(UnitTester $I): void
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
