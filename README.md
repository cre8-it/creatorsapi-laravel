# Creators API Laravel Wrapper

Small Laravel wrapper around the Amazon Creators API PHP SDK.

## Configuration

Publish the config:

```bash
php artisan vendor:publish --tag=creatorsapi-config
```

Environment options:

```
CREATORSAPI_CREDENTIAL_ID=
CREATORSAPI_CREDENTIAL_SECRET=
CREATORSAPI_CREDENTIAL_VERSION=
CREATORSAPI_AUTH_ENDPOINT=
CREATORSAPI_HOST=https://creatorsapi.amazon
CREATORSAPI_USER_AGENT=creatorsapi-laravel
CREATORSAPI_DEBUG=false
CREATORSAPI_DEBUG_FILE=php://output
CREATORSAPI_TEMP_FOLDER=
CREATORSAPI_HTTP_TIMEOUT=30
CREATORSAPI_HTTP_CONNECT_TIMEOUT=10
CREATORSAPI_HTTP_PROXY=
CREATORSAPI_HTTP_VERIFY=true
```

## Usage

Examples below follow the SDK samples (`SampleSearchItems.php`, `SampleGetItems.php`), adapted for Laravel with fluent chaining.

### SearchItems

```php
use CreatorsApi\Laravel\Facades\CreatorsApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource;
use Amazon\CreatorsAPI\v1\ApiException;

try {
    $response = CreatorsApi::searchItems()
        ->marketplace('www.amazon.com')
        ->partnerTag('yourtag-20')
        ->keywords('Harry Potter')
        ->searchIndex('Books')
        ->itemCount(2)
        ->resources([
            SearchItemsResource::IMAGES_PRIMARY_MEDIUM,
            SearchItemsResource::ITEM_INFO_TITLE,
            SearchItemsResource::OFFERS_V2_LISTINGS_AVAILABILITY,
            SearchItemsResource::OFFERS_V2_LISTINGS_PRICE,
        ])
        ->send();
} catch (ApiException $e) {
    report($e);
}
```

### GetItems

```php
use CreatorsApi\Laravel\Facades\CreatorsApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource;
use Amazon\CreatorsAPI\v1\ApiException;

try {
    $response = CreatorsApi::getItems()
        ->marketplace('www.amazon.com')
        ->partnerTag('yourtag-20')
        ->itemIds(['B0DLFMFBJW', 'B0BFC7WQ6R', 'B00ZV9RDKK'])
        ->resources([
            GetItemsResource::IMAGES_PRIMARY_MEDIUM,
            GetItemsResource::ITEM_INFO_TITLE,
            GetItemsResource::ITEM_INFO_FEATURES,
            GetItemsResource::OFFERS_V2_LISTINGS_PRICE,
            GetItemsResource::OFFERS_V2_LISTINGS_AVAILABILITY,
        ])
        ->send();
} catch (ApiException $e) {
    report($e);
}
```
