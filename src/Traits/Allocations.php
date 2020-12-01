<?php

namespace TradeSafe\Api\Traits;

trait Allocations
{
    public function allocationStartDelivery($id)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'allocations.graphql',
            'allocationStartDelivery',
            [
                'id' => $id,
            ]
        ));

        return $apiResponse['data']['allocationStartDelivery'];
    }

    public function allocationAcceptDelivery($id)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'allocations.graphql',
            'allocationAcceptDelivery',
            [
                'id' => $id,
            ]
        ));

        return $apiResponse['data']['allocationAcceptDelivery'];
    }
}
