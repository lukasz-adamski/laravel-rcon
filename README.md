# Source RCON Protocol Service Provider for Laravel
This package is developed to provide Laravel Framework service allowing you to work with source RCON protocol.
You can read more protocol specification on this page: https://developer.valvesoftware.com/wiki/Source_RCON_Protocol

## Installation
1. Install composer package using command:
```
composer require lukasz-adamski/laravel-rcon
```

2. Add Service Provider in `config/app.php`:
```php
Adams\Rcon\RconServiceProvider::class,
```

3. Add Facade in `config/app.php`:
```php
'Rcon' => Adams\Rcon\Facades\Facade::class,
```

4. Publish configuration file to your project:
```php
php artisan vendor:publish --provider="Adams\Rcon\RconServiceProvider"
```

## Environment
You can setup environment variables to establish default RCON connection.
- `RCON_CONNECTION` - default RCON connection name stored in `config/rcon.php`,
- `RCON_HOST` - RCON server hostname,
- `RCON_PORT` - RCON server listening port,
- `RCON_PASSWORD` - passphrase used to authorize connection, you can use `null` to skip authorization,
- `RCON_TIMEOUT` - RCON server connection timeout.

## Testing
To run predefined test set use:
```bash
php vendor/bin/phpunit
```

## Usage
Below you have example controller implementation:
```php
<?php

namespace App\Http\Controllers;

use Rcon;
use App\Http\Controllers\Controller;

class SimpleRconController extends Controller
{
    /**
     * Execute status command on default RCON server.
     *
     * @return Response
     */
    public function defaultStatus()
    {
        $response = Rcon::command('status');

        return view('console', compact('response'));
    }

    /**
     * Execute status command on specified RCON connection.
     *
     * @return Response
     */
    public function gameServerStatus()
    {
        $response = Rcon::connection('game_server')
            ->command('status');

        return view('console', compact('response'));
    }
}
```