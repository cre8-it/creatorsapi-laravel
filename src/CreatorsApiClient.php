<?php

namespace CreatorsApi\Laravel;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent;
use InvalidArgumentException;

class CreatorsApiClient implements CreatorsApiClientInterface
{
    public function __construct(private DefaultApi $api)
    {
    }

    public function api(): DefaultApi
    {
        return $this->api;
    }

    public function searchItems(?string $marketplace = null, ?SearchItemsRequestContent $request = null)
    {
        if ($marketplace !== null || $request !== null) {
            if ($marketplace === null || $request === null) {
                throw new InvalidArgumentException('Marketplace and request are required when calling searchItems directly.');
            }

            return $this->api->searchItems($marketplace, $request);
        }

        return new SearchItemsBuilder($this->api);
    }

    public function getItems(?string $marketplace = null, ?GetItemsRequestContent $request = null)
    {
        if ($marketplace !== null || $request !== null) {
            if ($marketplace === null || $request === null) {
                throw new InvalidArgumentException('Marketplace and request are required when calling getItems directly.');
            }

            return $this->api->getItems($marketplace, $request);
        }

        return new GetItemsBuilder($this->api);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->api->{$name}(...$arguments);
    }
}
