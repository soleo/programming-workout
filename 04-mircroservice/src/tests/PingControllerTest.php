<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PingControllerTest extends TestCase
{
    /**
     * Ping Pong Test.
     *
     * @return void
     */
    public function testPingPong()
    {
        $this->visit('/ping')
             ->see('pong');
    }
}
