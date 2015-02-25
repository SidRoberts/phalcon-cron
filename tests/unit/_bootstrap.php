<?php
// Here you can initialize variables that will be available to your tests


class TaskTask extends \Phalcon\Cli\Task
{
    public function actionAction($params)
    {
        sleep(1);
        
        print_r($params);
    }
}
