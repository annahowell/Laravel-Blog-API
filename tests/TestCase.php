<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const HEADER = [
        'Accept' => 'application/json'
    ];

    protected const URL_PREFIX = 'api/v1/';

    public function setUp(): void
    {
        parent::setUp();
    }
}
