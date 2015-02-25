<?php

namespace Job;

class PhalconTest extends \Codeception\TestCase\Test
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








    protected function getDi()
    {
        $di = new \Phalcon\Di\FactoryDefault\Cli();
        
        $di->setShared(
            "cron",
            function () {
                $cron = new \Sid\Phalcon\Cron\Manager();
                
                $cron->add(
                    new \Sid\Phalcon\Cron\Job\Phalcon(
                        "* * * * *",
                        "task",
                        "action",
                        [
                            "param1",
                            "param2",
                            "param3"
                        ]
                    )
                );
                
                return $cron;
            }
        );
        
        $di->set(
            "console",
            function () {
                $console = new \Phalcon\Cli\Console();
                
                return $console;
            }
        );
        
        return $di;
    }







    // tests
    public function testGetters()
    {
        $di = $this->getDi();
        
        
        
        $jobs = $di->get("cron")->getDueJobs();
        
        $this->assertEquals(count($jobs), 1);
        
        $job = $jobs[0];
        
        $this->assertEquals($job->getExpression(), "* * * * *");
        $this->assertEquals($job->getTask(), "task");
        $this->assertEquals($job->getAction(), "action");
        $this->assertEquals($job->getParams(), ["param1", "param2", "param3"]);
    }
    
    
    
    public function testRunningInForeground()
    {
        $di = $this->getDi();
        
        
        
        $outputs = $di->get("cron")->runInForeground();
        
        $this->assertEquals($outputs[0], print_r(["param1", "param2", "param3"], true));
    }
    
    
    
    public function testWaitJobs()
    {
        $di = $this->getDi();
        
        
        
        $processes = $di->get("cron")->runInBackground();
        
        foreach ($processes as $process) {
            $this->assertTrue($process->isRunning());
        }
        
        $di->get("cron")->wait();
        
        foreach ($processes as $process) {
            $this->assertFalse($process->isRunning());
        }
    }
    
    public function testTerminateJobs()
    {
        $di = $this->getDi();
        
        
        
        $processes = $di->get("cron")->runInBackground();
        
        foreach ($processes as $process) {
            $this->assertTrue($process->isRunning());
        }
        
        $di->get("cron")->terminate();
        
        foreach ($processes as $process) {
            $this->assertFalse($process->isRunning());
        }
    }
    
    public function testKillJobs()
    {
        $di = $this->getDi();
        
        
        
        $processes = $di->get("cron")->runInBackground();
        
        foreach ($processes as $process) {
            $this->assertTrue($process->isRunning());
        }
        
        $di->get("cron")->kill();
        
        foreach ($processes as $process) {
            $this->assertFalse($process->isRunning());
        }
    }
}
