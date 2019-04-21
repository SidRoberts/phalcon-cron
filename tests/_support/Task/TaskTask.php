<?php

namespace Tests\Task;

use Phalcon\Cli\Task;

class TaskTask extends Task
{
    public function actionAction($params)
    {
        sleep(1);

        print_r($params);
    }
}
