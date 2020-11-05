<?php

namespace TradeSafe\Api\Traits;

trait Tokens {
    public function createToken($args)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'tokens.graphql',
            'tokenCreate',
            $args
        ));

        return $apiResponse['data']['tokenCreate'];
    }

    public function getTokens($page = 1, $first = 10)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'tokens.graphql',
            'tokens',
            [
                'first' => $first,
                'page' => $page
            ]
        ));

        return $apiResponse['data']['tokens'];
    }

    public function getToken($id)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'tokens.graphql',
            'token',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['token'];
    }
}
