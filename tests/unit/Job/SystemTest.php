<?php

namespace Sid\Phalcon\Cron\Tests\Job;

use Codeception\TestCase\Test;
use Sid\Phalcon\Cron\Manager;
use Sid\Phalcon\Cron\Job\System as SystemJob;

class SystemTest extends Test
{
    public function testRunInForeground()
    {
        $cronJob = new SystemJob(
            "* * * * *",
            "echo 'hello world'"
        );

        $output = $cronJob->runInForeground();

        $this->assertEquals(
            $cronJob->getExpression(),
            "* * * * *"
        );

        $this->assertEquals(
            $cronJob->getCommand(),
            "echo 'hello world'"
        );

        $this->assertEquals(
            $output,
            "hello world\n"
        );
    }



    public function testSystemCronJobWithOutputToDevNull()
    {
        $systemCronJob = new SystemJob(
            "* * * * *",
            "echo 'hello world'",
            "/dev/null"
        );

        $this->assertEquals(
            $systemCronJob->runInForeground(),
            ""
        );
    }

    public function testSystemCronJobWithOutputToFile()
    {
        $tmpName = tempnam(
            sys_get_temp_dir(),
            "PHALCONCRON"
        );

        $systemCronJob = new SystemJob(
            "* * * * *",
            "echo 'hello world'",
            $tmpName
        );
        
        $systemCronJob->runInForeground();

        $this->assertEquals(
            "hello world\n",
            file_get_contents($tmpName)
        );
    }
    
    
    
    public function testSystemCronJobsInForeground()
    {
        $cron = new Manager();
        
        $systemCronJob1 = new SystemJob(
            "* * * * *",
            "echo 'hello world 1'"
        );

        $systemCronJob2 = new SystemJob(
            "* * * * *",
            "echo 'hello world 2'"
        );

        $systemCronJob3 = new SystemJob(
            "* * * * *",
            "echo 'hello world 3'"
        );

        $cron->add($systemCronJob1);
        $cron->add($systemCronJob2);
        $cron->add($systemCronJob3);

        $this->assertEquals(
            $cron->runInForeground(),
            [
                "hello world 1\n",
                "hello world 2\n",
                "hello world 3\n"
            ]
        );
    }

    public function testSystemCronJobsInBackground()
    {
        $systemCronJob = new SystemJob(
            "* * * * *",
            "sleep 1",
            "/dev/null"
        );

        $process = $systemCronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->wait();

        $this->assertFalse(
            $process->isRunning()
        );
    }



    public function testTerminateBackgroundCronJob()
    {
        $cronJob = new SystemJob(
            "* * * * *",
            "sleep 2"
        );

        $process = $cronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->terminate();

        $this->assertFalse(
            $process->isRunning()
        );
    }

    public function testKillBackgroundCronJob()
    {
        $cronJob = new SystemJob(
            "* * * * *",
            "sleep 2"
        );

        $process = $cronJob->runInBackground();

        $this->assertTrue(
            $process->isRunning()
        );

        $process->kill();

        $this->assertFalse(
            $process->isRunning()
        );
    }
}
