<?php

namespace Adams\Rcon;

use Adams\Rcon\Exceptions\RconException;

class Packet
{
    /**
     * Fillable field names in packet structure.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'type',
        'body'
    ];

    /**
     * Supported packet IDs.
     */
    const ID_AUTHORIZE = 5;
    const ID_COMMAND = 6;

    /**
     * Supported packet types.
     */
    const TYPE_SERVERDATA_AUTH = 3;
    const TYPE_SERVERDATA_AUTH_RESPONSE = 2;
    const TYPE_SERVERDATA_EXECCOMMAND = 2;
    const TYPE_SERVERDATA_RESPONSE_VALUE = 0;

    /**
     * Create packet from binary data received from
     * RCON server.
     * 
     * @param string $bytes
     * @return self
     */
    public static function fromBytes($bytes)
    {
        $packet = new self();
        $packet->setBytes($bytes);

        return $packet;
    }

    /**
     * Create packet from array data.
     * 
     * @param array $fields
     * @return self
     */
    public static function fromFields(array $fields)
    {
        $packet = new self();
        $packet->setFields($fields);

        return $packet;
    }

    /**
     * Prepare object with only fillable fields.
     * 
     * @param array $fields
     * @return void
     */
    protected function setFields(array $fields)
    {
        foreach ($this->fillable as $field)
        {
            if (! array_key_exists($field, $fields)) {
                throw new RconException("Invalid packet structure - missing $field field");
            }

            $this->{$field} = $fields[$field];
        }

        $this->encode();
    }

    /**
     * Get packet fillable fields with values.
     * 
     * @return array
     */
    public function getFields()
    {
        $result = [];

        foreach ($this->fillable as $field)
        {
            $result[$field] = $this->{$field};
        }

        return $result;
    }

    /**
     * Set binary data received from RCON server
     * and decode it to this object. 
     * 
     * @param string $bytes
     * @return void
     */
    public function setBytes($bytes)
    {
        $this->bytes = $bytes;

        $this->decode();
    }

    /**
     * Get binary packet structure compatible with
     * the RCON server.
     * 
     * @return string
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * Get packet data (payload only) size.
     * 
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get packet ID.
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get packet type identifier.
     * 
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get packet body. 
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Encode packet to binary data compatible with
     * RCON server protocol.
     * 
     * @return void
     */
    protected function encode()
    {
        $bytes = pack("VVZ*", $this->id, $this->type, $this->body);
        $bytes .= "\x00";

        $this->size = strlen($bytes);
        $this->bytes = pack("V", $this->size) . $bytes;
    }

    /**
     * Decode packet bytes to encapsulated data.
     * 
     * @return void
     */
    protected function decode()
    {
        if (is_null($this->bytes)) {
            return;
        }

        $fields = unpack(
            'V1size/V1id/V1type/Z*body', 
            $this->bytes
        );

        $this->setFields($fields);
    }
}