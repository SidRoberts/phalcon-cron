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
            count($jobs),
            1
        );

        $job = $jobs[0];

        $this->assertEquals(
            $job->getExpression(),
            "@hourly"
        );

        $this->assertEquals(
            $job->getCommand(),
            "sh backup.sh"
        );
    }

    public function testTwo()
    {
        $crontab2 = new CrontabParser(
            __DIR__ . "/crontabs/crontab2"
        );

        $jobs = $crontab2->getJobs();



        $this->assertEquals(
            count($jobs),
            2
        );



        $this->assertEquals(
            $jobs[0]->getExpression(),
            "@hourly"
        );

        $this->assertEquals(
            $jobs[0]->getCommand(),
            "sh purge-cache.sh"
        );



        $this->assertEquals(
            $jobs[1]->getExpression(),
            "* 0 * * *"
        );

        $this->assertEquals(
            $jobs[1]->getCommand(),
            "sh backup.sh"
        );
    }

    public function testThree()
    {
        $crontab3 = new CrontabParser(
            __DIR__ . "/crontabs/crontab3"
        );

        $jobs = $crontab3->getJobs();



        $this->assertEquals(
            count($jobs),
            3
        );



        $this->assertEquals(
            $jobs[0]->getExpression(),
            "@hourly"
        );

        $this->assertEquals(
            $jobs[0]->getCommand(),
            "sh purge-cache.sh"
        );



        $this->assertEquals(
            $jobs[1]->getExpression(),
            "* 0 * * *"
        );

        $this->assertEquals(
            $jobs[1]->getCommand(),
            "sh backup.sh"
        );



        $this->assertEquals(
            $jobs[2]->getExpression(),
            "0,30 1-12 * mon,wed,fri *"
        );

        $this->assertEquals(
            $jobs[2]->getCommand(),
            "php something.php"
        );
    }
}
