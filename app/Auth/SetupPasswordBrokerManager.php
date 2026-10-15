<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBroker;
use InvalidArgumentException;

class SetupPasswordBrokerManager extends PasswordBrokerManager
{
    protected function resolve($name): PasswordBroker
    {
        if ($name !== 'users') {
            return parent::resolve($name);
        }
        $config = $this->getConfig($name) ?? throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");

        if (($config['driver'] ?? 'database') !== 'database') {
            throw new InvalidArgumentException('Private account setup requires the database users password broker.');
        }
        $key = $this->app->make('config')['app.key'];
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $repository = new SetupTokenRepository(
            $this->app->make('db')->connection($config['connection'] ?? null),
            $this->app->make('hash'),
            $config['table'],
            $key,
            ($config['expire'] ?? 60) * 60,
            $config['throttle'] ?? 0,
        );

        return new SetupPasswordBroker(
            $repository,
            $this->app->make('auth')->createUserProvider($config['provider'] ?? null),
            $this->app->make('events'),
            timeboxDuration: $this->app->make('config')->get('auth.timebox_duration', 200000),
        );
    }
}
