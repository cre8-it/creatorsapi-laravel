# Creators API Laravel Wrapper

Small Laravel wrapper around the Amazon Creators API PHP SDK.

## Installation

```bash
composer require cre8-it/creatorsapi-laravel:^3.0
```

Version 3 requires `cre8-it/creatorsapi-php-sdk:^2.0`, which imports Amazon's
upstream SDK 1.3.0 with Login with Amazon (LWA) support. SDK package versions
and credential versions are separate: EU LWA credentials use version `3.2`.

## Requirements

- PHP 8.2+ (Laravel 13 requires PHP 8.3+)
- Laravel 12 or 13

The committed development lockfile targets Laravel 13 / PHP 8.3. The CI matrix also tests Laravel 12 / PHP 8.2 with Orchestra Testbench (see `.github/workflows/tests.yml`). To run the default suite locally:

```bash
composer install
vendor/bin/phpunit
```

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

Use the credential ID, secret, and version issued together by Amazon. For LWA,
the credential versions are `3.1` (NA), `3.2` (EU), and `3.3` (FE).
For `www.amazon.de`, use your issued EU version `3.2`; the SDK automatically
selects `https://api.amazon.co.uk/auth/o2/token` with the LWA scope and JSON body.
Leave `CREATORSAPI_AUTH_ENDPOINT` unset unless a custom endpoint is intentional;
remove any old Cognito endpoint override when migrating to LWA.

## Upgrading from 2.x

1. Generate LWA credentials in Amazon PartnerNet and update all three
   `CREATORSAPI_CREDENTIAL_*` values. Do not just change the version of legacy credentials.
2. Upgrade the consuming application's dependency and commit **its** lockfile:

   ```bash
   composer require cre8-it/creatorsapi-laravel:^3.0 --with-all-dependencies
   ```

3. Deploy with `composer install`, rebuild cached configuration with
   `php artisan config:cache`, and restart long-running queue/Horizon/Octane
   workers as applicable to your deployment.
4. Verify a real `getItems` or `searchItems` request using the correct marketplace
   and partner tag. The package tests use mocks, not live Amazon credentials.

The fluent wrapper API is unchanged. The underlying SDK removed
`OAuth2Config::setScope()`, requires a request argument on all
`DefaultApi::searchItems*` methods, and requires `partnerTag` for search requests.
These changes also affect applications using `CreatorsApi::api()` or forwarding
direct SDK calls through the facade. Legacy Cognito versions remain understood
by the SDK, but that does not guarantee Amazon still accepts legacy credentials.

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
