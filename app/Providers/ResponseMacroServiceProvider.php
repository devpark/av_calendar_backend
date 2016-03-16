<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $response)
    {
        // register API response
        $response->macro('api', function ($data, $status = 200, array $headers = []) {
            $output = [
                'response' => $data,
                'exec_time' => defined('LARAVEL_START')
                                ? round(microtime(true) - LARAVEL_START, 4)
                                : 0
            ];

            return $response->make($output, $status, $headers);
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
