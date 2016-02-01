Sid\Phalcon\Cron
================

Cron component for Phalcon.



[![Build Status](https://travis-ci.org/SidRoberts/phalcon-cron.svg?branch=master)](https://travis-ci.org/SidRoberts/phalcon-cron)
[![Build Status](https://scrutinizer-ci.com/g/SidRoberts/phalcon-cron/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SidRoberts/phalcon-cron/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SidRoberts/phalcon-cron/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SidRoberts/phalcon-cron/?branch=master)
[![Code Climate](https://codeclimate.com/github/SidRoberts/phalcon-cron/badges/gpa.svg)](https://codeclimate.com/github/SidRoberts/phalcon-cron)
[![Test Coverage](https://codeclimate.com/github/SidRoberts/phalcon-cron/badges/coverage.svg)](https://codeclimate.com/github/SidRoberts/phalcon-cron/coverage)



## Installing ##

Install using Composer:

```json
{
	"require": {
		"sidroberts/phalcon-cron": "dev-master"
	}
}
```

You'll also need to add the `console` to the DI:

```php
$di = new \Phalcon\Di();

// ...

$console = new \Phalcon\Cli\Console();

// ...

$di->setShared("console", $console);

// ...

$console->handle($arguments);
```


## Example ##

### Crontab ###

```
* * * * * /usr/bin/php /path/to/cli.php cron
```

### DI ###

```php
$di->set(
	"cron",
	function () {
		$cron = new \Sid\Phalcon\Cron\Manager();
		
		$cron->add(
			new \Sid\Phalcon\Cron\Job\Callback(
				"* * * * *",
				function () {
					// ...
				}
			)
		);
		
		$cron->add(
			new \Sid\Phalcon\Cron\Job\Phalcon(
				"0 * * * *",
				[
					"task"   => "task",
					"action" => "action",
					"params" => "params"
				]
			)
		);
		
		$cron->add(
			new \Sid\Phalcon\Cron\Job\System(
				"* 0 * * *",
				"sh backup.sh"
			)
		);
		
		return $cron;
	}
);
```

### CLI Task ###

```php
class CronTask extends \Phalcon\Cli\Task
{
	public function mainAction()
	{
		$this->cron->runInBackground();
	}
}
```



## Foreground Versus Background ##

Running jobs in the foreground (sequential):

    Job1 -------->
    Job2          ----------------->
    Job3                            ---->

Running jobs in the background (parallel):

    Job1 -------->
    Job2 ----------------->
    Job3 ---->

For most applications it is recommended to use `->runInBackground()` as this is typical of a Cron implementation and is often quicker. If you specifically need to access the output of each Cron Job, use `->runInForeground()`.

`->runInBackground()` returns an array of Process instances. `->runInForeground()` returns an array of outputs.



## Waiting, Terminating And Killing ##

By default all background processes register a [shutdown function](http://php.net/manual/en/function.register-shutdown-function.php) that forces the PHP script to wait for job to complete before shutting down. You can call `->wait()` on a Process instance if you need to wait until it has finished.

You can also use `->terminate()` and `->kill()` on a Process to send terminate and kill signals.

`->wait()`, `->terminate()` and `->kill()` are also available on the Manager instance and will wait for, terminate or kill every process.



## Running Jobs At A Custom Time ##

You can see which Jobs are due at a particular time by passing a `\DateTime` to `->getDueJobs()`:

```php
$datetime = new \DateTime("2015-01-01 00:00:00");

$cron->getDueJobs($datetime);
```

You can also pass a \DateTime to `->runInForeground()`/`->runInBackground()` to run jobs due at that particular time.

```php
$datetime = new \DateTime("2015-01-01 00:00:00");

$cron->runInBackground($datetime);
```



### Adding Jobs From A Crontab ###

You can also add jobs from a Crontab file, such as this one:

```
* * * * * /usr/bin/php /path/to/cli.php cron
```

```php
$cron = new \Sid\Phalcon\Cron\Manager();

$cron->addCrontab("/path/to/crontab");
```

To get an array of System Job instances from the Crontab:

```php
$crontab = new \Sid\Phalcon\Cron\CrontabParser("/path/to/crontab");

$jobs = $crontab->getJobs();
```
