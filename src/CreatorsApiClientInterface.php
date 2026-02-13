<?php

namespace CreatorsApi\Laravel;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent;

interface CreatorsApiClientInterface
{
    public function searchItems(?string $marketplace = null, ?SearchItemsRequestContent $request = null);

    public function getItems(?string $marketplace = null, ?GetItemsRequestContent $request = null);

    public function api(): DefaultApi;
}
