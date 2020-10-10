<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  use DatabaseSetup;
  use CreatesApplication;

  protected function setUp(): void
  {
    parent::setUp();
    $this->setupDatabase();
  }
}
