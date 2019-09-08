<?php

namespace Adams\Rcon\Test;

use Adams\Rcon\Packet;
use Adams\Rcon\Exceptions\RconException;

class PacketTest extends TestCase
{
    /**
     * Check that packet contains all fillable fields
     * by exception.
     * 
     * @return void
     */
    public function testEmptyPacketException()
    {
        $this->expectException(RconException::class);

        Packet::fromFields([]);
    }

    /**
     * Check that generated packet is valid.
     * 
     * @return void
     */
    public function testPacketFromFields()
    {
        $packet = Packet::fromFields([
            'id' => Packet::ID_AUTHORIZE,
            'type' => Packet::TYPE_SERVERDATA_AUTH,
            'body' => 'this is simple body'
        ]);

        $this->assertSame(
            $packet->getBytes(),
            "\x1D\x00\x00\x00\x05\x00\x00\x00\x03\x00\x00\x00this is simple body\x00\x00"
        );
    }

    /**
     * Check that parsed packet is valid.
     * 
     * @return void
     */
    public function testPacketFromBytes()
    {
        $packet = Packet::fromBytes(
            "\x1D\x00\x00\x00\x05\x00\x00\x00\x03\x00\x00\x00this is simple body\x00\x00"
        );

        $this->assertSame($packet->getId(), Packet::ID_AUTHORIZE);
        $this->assertSame($packet->getType(), Packet::TYPE_SERVERDATA_AUTH);
        $this->assertSame($packet->getBody(), 'this is simple body');
    }

    /**
     * Check protocol compliance in two directions.
     * 
     * @return void
     */
    public function testProtocolCompliance()
    {
        $encoded = Packet::fromFields([
            'id' => Packet::ID_AUTHORIZE,
            'type' => Packet::TYPE_SERVERDATA_AUTH,
            'body' => 'this is simple body'
        ]);

        $decoded = Packet::fromBytes($encoded->getBytes());

        $this->assertSame($encoded->getBytes(), $decoded->getBytes());
        $this->assertSame($encoded->getId(), $decoded->getId());
        $this->assertSame($encoded->getType(), $decoded->getType());
        $this->assertSame($encoded->getBody(), $decoded->getBody());
    }
}