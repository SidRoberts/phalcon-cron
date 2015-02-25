<?php


class ManagerTest extends \Codeception\TestCase\Test
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
    public function testAddJobsToCron()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $cronJob1 = new \Sid\Phalcon\Cron\Job\Phalcon("* * * * *", "task", "action", "params");
        $cronJob2 = new \Sid\Phalcon\Cron\Job\System("* * * * *", "echo 'hello world'");
        
        $this->assertEquals(count($cron->getDueJobs()), 0);
        
        $cron->add($cronJob1);
        
        $this->assertEquals(count($cron->getDueJobs()), 1);
        
        $cron->add($cronJob2);
        
        $this->assertEquals(count($cron->getDueJobs()), 2);
    }
    
    
    
    public function testCronJobsInBackground()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $systemCronJob = new \Sid\Phalcon\Cron\Job\System("* * * * *", "sleep 1", null, "/dev/null");
        
        $cron->add($systemCronJob);
        
        $processes = $cron->runInBackground();
        
        $this->assertTrue($processes[0]->isRunning());
        
        $cron->wait();
        
        $this->assertFalse($processes[0]->isRunning());
    }
    
    public function testTerminateBackgroundCronJobs()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $systemCronJob   = new \Sid\Phalcon\Cron\Job\System("* * * * *", "sleep 2", null, "/dev/null");
        $callbackCronJob = new \Sid\Phalcon\Cron\Job\Callback("* * * * *", function () { sleep(2); });
        
        $cron->add($systemCronJob);
        $cron->add($callbackCronJob);
        
        $processes = $cron->runInBackground();
        
        $this->assertTrue($processes[0]->isRunning());
        $this->assertTrue($processes[1]->isRunning());
        
        $cron->terminate();
        
        $this->assertFalse($processes[0]->isRunning());
        $this->assertFalse($processes[1]->isRunning());
    }
    
    public function testKillBackgroundCronJobs()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $systemCronJob   = new \Sid\Phalcon\Cron\Job\System("* * * * *", "sleep 2", null, "/dev/null");
        $callbackCronJob = new \Sid\Phalcon\Cron\Job\Callback("* * * * *", function () { sleep(2); });
        
        $cron->add($systemCronJob);
        $cron->add($callbackCronJob);
        
        $processes = $cron->runInBackground();
        
        $this->assertTrue($processes[0]->isRunning());
        $this->assertTrue($processes[1]->isRunning());
        
        $cron->kill();
        
        $this->assertFalse($processes[0]->isRunning());
        $this->assertFalse($processes[1]->isRunning());
    }
}
