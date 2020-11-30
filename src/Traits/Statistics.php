<?php

namespace TradeSafe\Api\Traits;

trait Statistics
{
    public function getStatistics()
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'statistics.graphql',
            'statistics'
        ));

        return $apiResponse['data']['statistics'];
    }
}
