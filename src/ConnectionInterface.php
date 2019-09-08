<?php

namespace Adams\Rcon;

interface ConnectionInterface
{
    /**
     * Check if connection to RCON server is established.
     * 
     * @return bool
     */
    public function isConnected();
    
    /**
     * Send packet and wait for server response.
     * 
     * @param int $id
     * @param int $type
     * @param string $body
     * @return \Adams\Rcon\Packet
     */
    public function send($id, $type, $body);

    /**
     * Check if connection is authorized.
     * 
     * @return bool
     */
    public function isAuthorized();

    /**
     * Authorize connection with given password.
     * 
     * @param string $password
     * @return bool
     */
    public function authorize($password);

    /**
     * Execute given command on RCON server.
     * 
     * @param string $command
     * @return string
     * @throws Exception
     */
    public function command($command);
}