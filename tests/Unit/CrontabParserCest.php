<?php

namespace Tests\Unit;

use Sid\Phalcon\Cron\CrontabParser;
use Tests\Support\UnitTester;

class CrontabParserCest
{
    public function one(UnitTester $I)
    {
        $crontab1 = new CrontabParser(
            codecept_data_dir() . "/crontabs/crontab1"
        );

        $jobs = $crontab1->getJobs();

        $I->assertCount(
            1,
            $jobs
        );

        $job = $jobs[0];

        $I->assertEquals(
            "@hourly",
            $job->getExpression()
        );

        $I->assertEquals(
            "sh backup.sh",
            $job->getCommand()
        );
    }

    public function two(UnitTester $I)
    {
        $crontab2 = new CrontabParser(
            codecept_data_dir() . "/crontabs/crontab2"
        );

        $jobs = $crontab2->getJobs();



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

    public function three(UnitTester $I)
    {
        $crontab3 = new CrontabParser(
            codecept_data_dir() . "/crontabs/crontab3"
        );

        $jobs = $crontab3->getJobs();



        $I->assertCount(
            3,
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



        $I->assertEquals(
            "0,30 1-12 * mon,wed,fri *",
            $jobs[2]->getExpression()
        );

        $I->assertEquals(
            "php something.php",
            $jobs[2]->getCommand()
        );
    }
}
