<?php

namespace Tests\Job;

use Sid\Phalcon\Cron\Job\System as SystemJob;
use Sid\Phalcon\Cron\Manager;
use Tests\UnitTester;

class SystemCest
{
    public function runInForeground(UnitTester $I)
    {
        $cronJob = new SystemJob(
            "* * * * *",
            "echo 'hello world'"
        );

        $output = $cronJob->runInForeground();

        $I->assertEquals(
            "* * * * *",
            $cronJob->getExpression()
        );

        $I->assertEquals(
            "echo 'hello world'",
            $cronJob->getCommand()
        );

        $I->assertEquals(
            "hello world\n",
            $output
        );
    }



    public function systemCronJobWithOutputToDevNull(UnitTester $I)
    {
        $systemCronJob = new SystemJob(
            "* * * * *",
            "echo 'hello world'",
            "/dev/null"
        );

        $I->assertEquals(
            "",
            $systemCronJob->runInForeground()
        );
    }

    public function systemCronJobWithOutputToFile(UnitTester $I)
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

        $I->assertEquals(
            "hello world\n",
            file_get_contents($tmpName)
        );
    }
    
    
    
    public function systemCronJobsInForeground(UnitTester $I)
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

        $I->assertEquals(
            [
                "hello world 1\n",
                "hello world 2\n",
                "hello world 3\n",
            ],
            $cron->runInForeground()
        );
    }
}
