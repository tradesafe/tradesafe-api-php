<?php

namespace TradeSafe\Api\Traits;

trait Tokens
{
    public function createToken($args)
    {
        $operation = 'tokenUserCreate';

        if (isset($args['organizationName'])
            && isset($args['organizationType'])
            && isset($args['organizationRegistrationNumber'])) {
            $operation = 'tokenOrganizationCreate';
        }

        $apiResponse = self::callApi(self::createGraphQLRequest(
            'tokens.graphql',
            $operation,
            $args
        ));

        return $apiResponse['data']['tokenCreate'];
    }

    public function createTokenWithoutBankAccount($args)
    {
        $operation = 'tokenUserCreateWithoutBankAccount';

        if (isset($args['organizationName'])
            && isset($args['organizationType'])
            && isset($args['organizationRegistrationNumber'])) {
            $operation = 'tokenOrganizationCreateWithoutBankAccount';
        }

        $apiResponse = self::callApi(self::createGraphQLRequest(
            'tokens.graphql',
            $operation,
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
