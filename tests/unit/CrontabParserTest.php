<?php

namespace Sid\Phalcon\Cron\Tests;

use Codeception\TestCase\Test;
use Sid\Phalcon\Cron\CrontabParser;

class CrontabParserTest extends Test
{
    public function testOne()
    {
        $crontab1 = new CrontabParser(
            __DIR__ . "/crontabs/crontab1"
        );

        $jobs = $crontab1->getJobs();

        $this->assertEquals(
            1,
            count($jobs)
        );

        $job = $jobs[0];

        $this->assertEquals(
            "@hourly",
            $job->getExpression()
        );

        $this->assertEquals(
            "sh backup.sh",
            $job->getCommand()
        );
    }

    public function testTwo()
    {
        $crontab2 = new CrontabParser(
            __DIR__ . "/crontabs/crontab2"
        );

        $jobs = $crontab2->getJobs();



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

    public function testThree()
    {
        $crontab3 = new CrontabParser(
            __DIR__ . "/crontabs/crontab3"
        );

        $jobs = $crontab3->getJobs();



        $this->assertEquals(
            3,
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



        $this->assertEquals(
            "0,30 1-12 * mon,wed,fri *",
            $jobs[2]->getExpression()
        );

        $this->assertEquals(
            "php something.php",
            $jobs[2]->getCommand()
        );
    }
}
