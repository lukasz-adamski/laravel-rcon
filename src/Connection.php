<?php

namespace Adams\Rcon;

class Connection implements ConnectionInterface
{
    /**
     * Stores the value of whether authorization 
     * has been done.
     * 
     * @var bool
     */
    protected $authorized = false;

    /**
     * RCON server socket handle.
     * 
     * @var resource|void
     */
    protected $socket;

    /**
     * Create new connection instance.
     * 
     * @param string $host
     * @param int $port
     * @param int $timeout
     */
    public function __construct($host, $port, $timeout = 60)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * Connect to RCON server.
     * 
     * @throws Exception
     * @return void
     */
    public function connect()
    {
        $this->socket = fsockopen($this->host, $this->port, $errno, 
            $errstr, $this->timeout);

        if (! $this->isConnected()) {
            throw new Exception("Socket error: $errstr ($errno)");
        }
    }

    /**
     * Disconnect from RCON server if connection is established.
     * 
     * @return void
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            fclose($this->socket);
        }
    }

    /**
     * Check if connection to RCON server is established.
     * 
     * @return bool
     */
    public function isConnected()
    {
        return is_resource($this->socket);
    }

    /**
     * Check if connection is established before
     * sending data.
     * 
     * @return void
     * @throws Exception
     */
    protected function checkConnection()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }
    }

    /**
     * Authorize connection with given password.
     * 
     * @param string $password
     * @return bool
     */
    public function authorize($password)
    {
        $this->checkConnection();

        $this->authorized = false;

        $response = $this->send(
            Packet::ID_AUTHORIZE, Packet::TYPE_SERVERDATA_AUTH, $password
        );

        if ($response->getId() == Packet::ID_AUTHORIZE &&
            $response->getType() == Packet::TYPE_SERVERDATA_AUTH_RESPONSE) {
            $this->authorized = true;
        }

        return $this->isAuthorized();
    }

    /**
     * Check if connection is authorized.
     * 
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->authorized;
    }

    /**
     * Execute given command on RCON server.
     * 
     * @param string $command
     * @return string
     * @throws Exception
     */
    public function command($command)
    {
        $this->checkConnection();

        $response = $this->send(
            Packet::ID_COMMAND, Packet::TYPE_SERVERDATA_EXECCOMMAND, $command
        );

        if ($response->getId() == Packet::ID_COMMAND &&
            $response->getType() == Packet::TYPE_SERVERDATA_RESPONSE_VALUE) {
            return $response->getBody();
        }

        throw new Exception('Received invalid response');
    }

    /**
     * Send packet do RCON server and receive response.
     * 
     * @param int $id
     * @param int $type
     * @param string $body
     * @return Packet
     */
    public function send($id, $type, $body)
    {
        $this->sendPacket($id, $type, $body);

        return $this->receivePacket();
    }

    /**
     * Generate packet binary structure used by RCON server
     * and send it through socket.
     * 
     * @param int $id
     * @param int $type
     * @param string $body
     * @return void
     */
    public function sendPacket($id, $type, $body)
    {
        $this->checkConnection();

        $packet = Packet::fromFields(
            compact('id', 'type', 'body')
        );

        fwrite($this->socket, $packet->getBytes());
    }

    /**
     * Receive packet from RCON server and decode its
     * binary structure to data object. 
     * 
     * @return Packet
     */
    public function receivePacket()
    {
        $this->checkConnection();

        $bytes = fread($this->socket, 4);
        $size = unpack('V1size', $bytes);

        $bytes .= fread($this->socket, $size['size']);

        return Packet::fromBytes($bytes);
    }
}