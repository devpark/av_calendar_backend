<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Log
     */
    protected $log;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['log']->getMonolog()->pushProcessor(function ($record) {
            $auth = $this->app['auth'];
            $request = $this->app['request'];

            $record['extra'] = [
                'user' => [
                    'id' => $auth->id() ?: 0,
                    'ip' => $request->getClientIp(),
                ],
            ];

            // try to get artisan command
            $command = $request->server('argv');

            // if artisan command - include it in log
            if ($command) {
                $record['extra']['command'] = is_array($command) ? implode(' ', $command) : $command;

                return $record;
            }

            // if via HTTP - add HTTP data
            $record['extra']['request'] = [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->all(),
            ];

            return $record;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
