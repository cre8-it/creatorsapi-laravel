<?php

namespace CreatorsApi\Laravel;

use Amazon\CreatorsAPI\v1\com\amazon\creators\api\DefaultApi;
use Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent;
use InvalidArgumentException;

class GetItemsBuilder
{
    private ?string $marketplace = null;
    private GetItemsRequestContent $request;

    public function __construct(private DefaultApi $api)
    {
        $this->request = new GetItemsRequestContent();
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

    public function itemIds(array $itemIds): self
    {
        $this->request->setItemIds($itemIds);

        return $this;
    }

    public function resources(array $resources): self
    {
        $this->request->setResources($resources);

        return $this;
    }

    public function request(GetItemsRequestContent $request): self
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

        return $this->api->getItems($marketplace, $this->request);
    }

    public function getRequest(): GetItemsRequestContent
    {
        return $this->request;
    }
}
