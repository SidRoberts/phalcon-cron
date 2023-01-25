<?php

namespace Tests\Support\Task;

use Phalcon\Cli\Task;

class TaskTask extends Task
{
    public function actionAction($param1, $param2, $param3)
    {
        sleep(1);

        echo $param1 . PHP_EOL . $param2 . PHP_EOL . $param3;
    }
}
