<?php

namespace App\Auth;

use Closure;
use Illuminate\Auth\Passwords\PasswordBrokerManager as LaravelPasswordBrokerManager;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PasswordBrokerManager extends LaravelPasswordBrokerManager{


    /**
     * @param string $name
     * @return PasswordBroker|\Illuminate\Auth\Passwords\PasswordBroker|\Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'])
        );
    }

    /**
     * @param array $config
     * @return DatabaseTokenRepository|\Illuminate\Auth\Passwords\DatabaseTokenRepository|\Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    public function sendResetLink(array $credentials,Closure $callback=null)
    {
    }

    public function reset(array $credentials, Closure $callback)
    {
    }
}
