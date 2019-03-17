<?php

namespace Sid\Phalcon\Cron\Tests;

use Codeception\TestCase\Test;
use DateTime;
use Sid\Phalcon\Cron\Manager;
use Sid\Phalcon\Cron\Job\System as SystemJob;

class JobTest extends Test
{
    public function testPredefinedExpressions()
    {
        $cron = new Manager();

        $yearlyCronJob  = new SystemJob("@yearly", "echo 'yearly'");
        $monthlyCronJob = new SystemJob("@monthly", "echo 'monthly'");
        $weeklyCronJob  = new SystemJob("@weekly", "echo 'weekly'");
        $dailyCronJob   = new SystemJob("@daily", "echo 'daily'");
        $hourlyCronJob  = new SystemJob("@hourly", "echo 'hourly'");

        $cron->add($yearlyCronJob);
        $cron->add($monthlyCronJob);
        $cron->add($weeklyCronJob);
        $cron->add($dailyCronJob);
        $cron->add($hourlyCronJob);

        $year  = new DateTime("2015-01-01 00:00:00");
        $month = new DateTime("2015-01-01 00:00:00");
        $week  = new DateTime("2015-01-04 00:00:00"); // Sunday
        $day   = new DateTime("2015-01-02 00:00:00");
        $hour  = new DateTime("2015-01-01 15:00:00");

        $this->assertEquals(
            [
                "yearly\n",
                "monthly\n",
                "daily\n",
                "hourly\n",
            ],
            $cron->runInForeground($year)
        );

        $this->assertEquals(
            [
                "yearly\n",
                "monthly\n",
                "daily\n",
                "hourly\n",
            ],
            $cron->runInForeground($month)
        );

        $this->assertEquals(
            [
                "weekly\n",
                "daily\n",
                "hourly\n",
            ],
            $cron->runInForeground($week)
        );

        $this->assertEquals(
            [
                "daily\n",
                "hourly\n",
            ],
            $cron->runInForeground($day)
        );

        $this->assertEquals(
            [
                "hourly\n",
            ],
            $cron->runInForeground($hour)
        );
    }
}
