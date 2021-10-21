<?php

namespace NotificationChannels\HismsWs;

use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class HismsWsChannelServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
         $this->mergeConfigFrom(__DIR__.'/../config/hismsws.php', 'hismsws');

        $this->publishes([
            __DIR__.'/../config/hismsws.php' => config_path('hismsws.php'),
        ]);

     $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'hismsws');

        $this->app->bind(HismsWsConfig::class, function () {
            return new HismsWsConfig($this->app['config']['hismsws']);
        });

        $this->app->singleton(HismsWsService::class, function (Application $app) {
            /** @var HismsWsConfig $config */
            $config = $app->make(HismsWsConfig::class);

           // try{
                return new HismsWsService($config,
                        new Client($this->app['config']['hismsws']['guzzle']['client'])
                    );
            //}
            throw InvalidConfigException::missingConfig();
        });


        $this->app->singleton(HismsWsChannel::class, function (Application $app) {
            return new HismsWsChannel(
                $app->make(HismsWsService::class),
                $app->make(Dispatcher::class)
            );
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            HismsWsConfig::class,
            HismsWsService::class,
            HismsWsChannel::class,
        ];
    }
}
