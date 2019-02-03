<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Exceptions\Handler;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected function setUp()
    {
        parent::setUp();
        $this->seedMigrations();
    }
    protected function seedMigrations()
    {
        (new \DatabaseSeeder())->run();
    }
    protected function disableExceptionHandling()
    {
        $this->app->instance(Handler::class, new class () extends Handler
        {
            public function __construct()
            {
            }

            public function report(\Exception $e)
            {
            }

            public function render($request, \Exception $e)
            {
                throw $e;
            }
        }
        );
    }





}
