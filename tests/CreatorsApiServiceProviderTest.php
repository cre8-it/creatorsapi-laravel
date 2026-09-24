<?php

namespace CreatorsApi\Laravel\Tests;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\Configuration;
use CreatorsApi\Laravel\CreatorsApiClient;
use CreatorsApi\Laravel\CreatorsApiClientInterface;
use CreatorsApi\Laravel\CreatorsApiServiceProvider;
use CreatorsApi\Laravel\Facades\CreatorsApi;
use CreatorsApi\Laravel\GetItemsBuilder;
use CreatorsApi\Laravel\SearchItemsBuilder;
use GuzzleHttp\ClientInterface;
use Orchestra\Testbench\TestCase;

class CreatorsApiServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [CreatorsApiServiceProvider::class];
    }

    public function test_provider_configures_the_sdk_and_http_client(): void
    {
        config()->set('creatorsapi.credentials.id', 'example-id');
        config()->set('creatorsapi.credentials.secret', 'example-secret');
        config()->set('creatorsapi.http.timeout', 5);
        config()->set('creatorsapi.http.proxy', null);

        $sdkConfig = $this->app->make(Configuration::class);
        $this->assertSame('example-id', $sdkConfig->getCredentialId());
        $this->assertSame('example-secret', $sdkConfig->getCredentialSecret());

        $http = $this->app->make(ClientInterface::class);
        $this->assertSame(5, $http->getConfig('timeout'));
        $this->assertNull($http->getConfig('proxy'));

        $this->assertSame($http, $this->app->make(ClientInterface::class));
        $this->assertInstanceOf(DefaultApi::class, $this->app->make(DefaultApi::class));
    }

    public function test_facade_and_container_aliases_resolve_the_same_sdk_wrapper(): void
    {
        $client = $this->app->make(CreatorsApiClientInterface::class);

        $this->assertInstanceOf(CreatorsApiClient::class, $client);
        $this->assertSame($client, $this->app->make('creatorsapi'));
        $this->assertSame($client, $this->app->make(CreatorsApiClient::class));
        $this->assertSame($this->app->make(DefaultApi::class), CreatorsApi::api());

        $getItems = CreatorsApi::getItems()->marketplace('www.amazon.de')
            ->partnerTag('example-tag-20')->itemIds(['B012345678']);
        $this->assertInstanceOf(GetItemsBuilder::class, $getItems);
        $this->assertSame(['B012345678'], $getItems->getRequest()->getItemIds());

        $search = CreatorsApi::searchItems()->marketplace('www.amazon.de')
            ->partnerTag('example-tag-20')->keywords('books');
        $this->assertInstanceOf(SearchItemsBuilder::class, $search);
        $this->assertSame('books', $search->getRequest()->getKeywords());
    }
}
