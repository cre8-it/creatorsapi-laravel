<?php

namespace CreatorsApi\Laravel\Tests;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent;
use CreatorsApi\Laravel\CreatorsApiClient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CreatorsApiClientTest extends TestCase
{
    public function test_get_items_builder_sends_the_request_to_the_sdk(): void
    {
        $api = $this->createMock(DefaultApi::class);
        $response = new GetItemsResponseContent();
        $api->expects($this->once())->method('getItems')
            ->with('www.amazon.de', $this->callback(function (GetItemsRequestContent $request): bool {
                $this->assertSame('example-tag-21', $request->getPartnerTag());
                $this->assertSame(['B012345678'], $request->getItemIds());
                $this->assertSame([GetItemsResource::ITEM_INFO_TITLE], $request->getResources());

                return true;
            }))
            ->willReturn($response);

        $client = new CreatorsApiClient($api);

        $this->assertSame($response, $client->getItems()
            ->marketplace('www.amazon.de')
            ->partnerTag('example-tag-21')
            ->itemIds(['B012345678'])
            ->resources([GetItemsResource::ITEM_INFO_TITLE])
            ->send());
    }

    public function test_search_builder_supplies_the_required_request_and_partner_tag(): void
    {
        $api = $this->createMock(DefaultApi::class);
        $response = new SearchItemsResponseContent();
        $api->expects($this->once())->method('searchItems')
            ->with('www.amazon.de', $this->callback(function (SearchItemsRequestContent $request): bool {
                $this->assertSame('example-tag-21', $request->getPartnerTag());
                $this->assertSame('books', $request->getKeywords());
                $this->assertSame('Books', $request->getSearchIndex());
                $this->assertSame(2, $request->getItemCount());
                $this->assertSame([SearchItemsResource::ITEM_INFO_TITLE], $request->getResources());
                $this->assertTrue($request->valid());

                return true;
            }))
            ->willReturn($response);

        $client = new CreatorsApiClient($api);

        $this->assertSame($response, $client->searchItems()
            ->marketplace('www.amazon.de')
            ->partnerTag('example-tag-21')
            ->keywords('books')
            ->searchIndex('Books')
            ->itemCount(2)
            ->resources([SearchItemsResource::ITEM_INFO_TITLE])
            ->send());
    }

    public function test_direct_get_items_requests_are_forwarded_unchanged(): void
    {
        $api = $this->createMock(DefaultApi::class);
        $request = new GetItemsRequestContent(['partnerTag' => 'example-tag-21', 'itemIds' => ['B012345678']]);
        $response = new GetItemsResponseContent();
        $api->expects($this->once())->method('getItems')
            ->with('www.amazon.de', $this->identicalTo($request))->willReturn($response);

        $this->assertSame($response, (new CreatorsApiClient($api))->getItems('www.amazon.de', $request));
    }

    public function test_direct_search_requests_are_forwarded_unchanged(): void
    {
        $api = $this->createMock(DefaultApi::class);
        $request = new SearchItemsRequestContent(['partnerTag' => 'example-tag-21', 'keywords' => 'books']);
        $response = new SearchItemsResponseContent();
        $api->expects($this->once())->method('searchItems')
            ->with('www.amazon.de', $this->identicalTo($request))->willReturn($response);

        $this->assertSame($response, (new CreatorsApiClient($api))->searchItems('www.amazon.de', $request));
    }

    public function test_search_builder_still_requires_a_marketplace(): void
    {
        $api = $this->createMock(DefaultApi::class);
        $api->expects($this->never())->method('searchItems');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Marketplace is required.');

        (new CreatorsApiClient($api))->searchItems()->partnerTag('example-tag-21')->keywords('books')->send();
    }
}
