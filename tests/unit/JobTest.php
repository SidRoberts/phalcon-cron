<?php


class JobTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testPredefinedExpressions()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $yearlyCronJob  = new \Sid\Phalcon\Cron\Job\System("@yearly",  "echo 'yearly'");
        $monthlyCronJob = new \Sid\Phalcon\Cron\Job\System("@monthly", "echo 'monthly'");
        $weeklyCronJob  = new \Sid\Phalcon\Cron\Job\System("@weekly",  "echo 'weekly'");
        $dailyCronJob   = new \Sid\Phalcon\Cron\Job\System("@daily",   "echo 'daily'");
        $hourlyCronJob  = new \Sid\Phalcon\Cron\Job\System("@hourly",  "echo 'hourly'");
        
        $cron->add($yearlyCronJob);
        $cron->add($monthlyCronJob);
        $cron->add($weeklyCronJob);
        $cron->add($dailyCronJob);
        $cron->add($hourlyCronJob);
        
        $year  = new \DateTime("2015-01-01 00:00:00");
        $month = new \DateTime("2015-01-01 00:00:00");
        $week  = new \DateTime("2015-01-04 00:00:00"); // Sunday
        $day   = new \DateTime("2015-01-02 00:00:00");
        $hour  = new \DateTime("2015-01-01 15:00:00");
        
        $this->assertEquals(
            $cron->runInForeground($year),
            [
                "yearly\n",
                "monthly\n",
                "daily\n",
                "hourly\n"
            ]
        );
        
        $this->assertEquals(
            $cron->runInForeground($month),
            [
                "yearly\n",
                "monthly\n",
                "daily\n",
                "hourly\n"
            ]
        );
        
        $this->assertEquals(
            $cron->runInForeground($week),
            [
                "weekly\n",
                "daily\n",
                "hourly\n"
            ]
        );
        
        $this->assertEquals(
            $cron->runInForeground($day),
            [
                "daily\n",
                "hourly\n"
            ]
        );
        
        $this->assertEquals(
            $cron->runInForeground($hour),
            [
                "hourly\n"
            ]
        );
    }
}
