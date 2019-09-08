<?php

namespace Adams\Rcon\Test;

use Rcon;
use Adams\Rcon\Connection;

class RconTest extends TestCase
{
    /**
     * Check default connection status.
     * 
     * @return void
     */
    public function testDefaultConnectionStatus()
    {
        $this->assertFalse(Rcon::isConnected());
    }

    /**
     * Check that connection cannot be established to 
     * default server.
     * 
     * @return void
     */
    public function testConnectionException()
    {
        $this->expectException(\ErrorException::class);

        Rcon::defaultConnection();
    }
}