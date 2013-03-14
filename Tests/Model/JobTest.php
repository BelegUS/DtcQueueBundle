<?php
namespace Dtc\QueueBundle\Test\Model;

use Dtc\QueueBundle\Model\Job;
use Dtc\QueueBundle\Tests\FibonacciWorker;
use Dtc\QueueBundle\Tests\StaticJobManager;
use Dtc\QueueBundle\Model\WorkerManager;

use Monolog\Logger;

class JobTest
    extends \PHPUnit_Framework_TestCase
{
    public function testSetArgs() {
        $worker = new FibonacciWorker();
        $job = new Job($worker, new \DateTime(), false, null);
        $job->setArgs(array(1));
        $job->setArgs(array(1, array(1,2)));

        try {
            $job->setArgs(array($job));
            $this->fail("Invalid job argument passed");
        } catch (\Exception $e) {
        }

        try {
            $job->setArgs(array(1, array($job)));
            $this->fail("Invalid job argument passed");
        } catch (\Exception $e) {
        }
    }

    public function testChainJobCall() {
        $jobManager = new StaticJobManager();
        $worker = new FibonacciWorker();
        $worker->setJobManager($jobManager);

        $job = new Job($worker, new \DateTime(), false, null);
        $this->assertNull($job->getId(), "Job id should be null");

        $job->fibonacci(1);
        $this->assertNotNull($job->getId(), "Job id should be generated");

        try {
            $job->invalidFunctionCall();
            $this->fail("invalid chain, should fail");
        } catch (\Exception $e) {
        }
    }
}