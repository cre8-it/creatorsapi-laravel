<?php

namespace CreatorsApi\Laravel;

use Amazon\CreatorsAPI\v1\Configuration;
use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class CreatorsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/creatorsapi.php', 'creatorsapi');

        $this->app->singleton(Configuration::class, function (Container $app) {
            $settings = $app->make('config')->get('creatorsapi', []);

            $config = new Configuration();

            $credentials = $settings['credentials'] ?? [];
            if (array_key_exists('id', $credentials) && $credentials['id'] !== '') {
                $config->setCredentialId($credentials['id']);
            }
            if (array_key_exists('secret', $credentials) && $credentials['secret'] !== '') {
                $config->setCredentialSecret($credentials['secret']);
            }
            if (array_key_exists('version', $credentials) && $credentials['version'] !== '') {
                $config->setVersion($credentials['version']);
            }

            $auth = $settings['auth'] ?? [];
            if (array_key_exists('endpoint', $auth) && $auth['endpoint'] !== '') {
                $config->setAuthEndpoint($auth['endpoint']);
            }

            $client = $settings['client'] ?? [];
            if (array_key_exists('host', $client) && $client['host'] !== '') {
                $config->setHost($client['host']);
            }
            if (array_key_exists('user_agent', $client) && $client['user_agent'] !== '') {
                $config->setUserAgent($client['user_agent']);
            }
            if (array_key_exists('debug_file', $client) && $client['debug_file'] !== '') {
                $config->setDebugFile($client['debug_file']);
            }
            if (array_key_exists('temp_folder_path', $client) && $client['temp_folder_path'] !== '') {
                $config->setTempFolderPath($client['temp_folder_path']);
            }

            if (array_key_exists('debug', $client)) {
                $config->setDebug((bool) $client['debug']);
            }

            return $config;
        });

        $this->app->singleton(DefaultApi::class, function (Container $app) {
            $settings = $app->make('config')->get('creatorsapi', []);
            $httpOptions = $settings['http'] ?? [];
            $httpOptions = $this->filterNulls($httpOptions);

            $client = new Client($httpOptions);

            return new DefaultApi($client, $app->make(Configuration::class));
        });

        $this->app->alias(DefaultApi::class, 'creatorsapi');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/creatorsapi.php' => $this->getConfigPath(),
            ], 'creatorsapi-config');
        }
    }

    private function filterNulls(array $values): array
    {
        return array_filter($values, function ($value) {
            return $value !== null;
        });
    }

    private function getConfigPath(): string
    {
        if (method_exists($this->app, 'configPath')) {
            return $this->app->configPath('creatorsapi.php');
        }

        return $this->app->basePath('config' . DIRECTORY_SEPARATOR . 'creatorsapi.php');
    }
}
