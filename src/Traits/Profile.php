<?php

namespace TradeSafe\Api\Traits;

trait Profile
{
    public function getProfile()
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'profiles.graphql',
            'apiProfile'
        ));

        return $apiResponse['data']['apiProfile'];
    }
}
