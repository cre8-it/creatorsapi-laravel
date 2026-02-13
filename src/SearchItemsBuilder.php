<?php

namespace CreatorsApi\Laravel;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent;
use InvalidArgumentException;

class SearchItemsBuilder
{
    private ?string $marketplace = null;
    private SearchItemsRequestContent $request;

    public function __construct(private DefaultApi $api)
    {
        $this->request = new SearchItemsRequestContent();
    }

    public function marketplace(string $marketplace): self
    {
        $this->marketplace = $marketplace;

        return $this;
    }

    public function partnerTag(string $partnerTag): self
    {
        $this->request->setPartnerTag($partnerTag);

        return $this;
    }

    public function keywords(string $keywords): self
    {
        $this->request->setKeywords($keywords);

        return $this;
    }

    public function searchIndex(string $searchIndex): self
    {
        $this->request->setSearchIndex($searchIndex);

        return $this;
    }

    public function itemCount(int $itemCount): self
    {
        $this->request->setItemCount($itemCount);

        return $this;
    }

    public function resources(array $resources): self
    {
        $this->request->setResources($resources);

        return $this;
    }

    public function request(SearchItemsRequestContent $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function send(?string $marketplace = null)
    {
        $marketplace = $marketplace ?? $this->marketplace;

        if ($marketplace === null || $marketplace === '') {
            throw new InvalidArgumentException('Marketplace is required.');
        }

        return $this->api->searchItems($marketplace, $this->request);
    }

    public function getRequest(): SearchItemsRequestContent
    {
        return $this->request;
    }
}
