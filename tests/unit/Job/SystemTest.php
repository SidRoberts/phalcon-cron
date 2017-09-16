<?php

namespace Sid\Phalcon\Cron\Tests\Job;

class SystemTest extends \Codeception\TestCase\Test
{
    public function testRunInForeground()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "echo 'hello world'"
        );

        $output = $cronJob->runInForeground();

        $this->assertEquals($cronJob->getExpression(), "* * * * *");
        $this->assertEquals($cronJob->getCommand(), "echo 'hello world'");

        $this->assertEquals($output, "hello world\n");
    }



    public function testSystemCronJobWithOutputToDevNull()
    {
        $systemCronJob = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "echo 'hello world'",
            "/dev/null"
        );

        $this->assertEquals($systemCronJob->runInForeground(), "");
    }

    public function testSystemCronJobWithOutputToFile()
    {
        $tmpName = tempnam(sys_get_temp_dir(), "PHALCONCRON");

        $systemCronJob = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "echo 'hello world'",
            $tmpName
        );
        
        $systemCronJob->runInForeground();

        $this->assertEquals("hello world\n", file_get_contents($tmpName));
    }
    
    
    
    public function testSystemCronJobsInForeground()
    {
        $cron = new \Sid\Phalcon\Cron\Manager();
        
        $systemCronJob1 = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "echo 'hello world 1'"
        );

        $systemCronJob2 = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "echo 'hello world 2'"
        );

        $systemCronJob3 = new \Sid\Phalcon\Cron\Job\System(
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
        $systemCronJob = new \Sid\Phalcon\Cron\Job\System(
            "* * * * *",
            "sleep 1",
            "/dev/null"
        );

        $process = $systemCronJob->runInBackground();

        $this->assertTrue($process->isRunning());

        $process->wait();

        $this->assertFalse($process->isRunning());
    }



    public function testTerminateBackgroundCronJob()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\System("* * * * *", "sleep 2");

        $process = $cronJob->runInBackground();

        $this->assertTrue($process->isRunning());

        $process->terminate();

        $this->assertFalse($process->isRunning());
    }

    public function testKillBackgroundCronJob()
    {
        $cronJob = new \Sid\Phalcon\Cron\Job\System("* * * * *", "sleep 2");

        $process = $cronJob->runInBackground();

        $this->assertTrue($process->isRunning());

        $process->kill();

        $this->assertFalse($process->isRunning());
    }
}
