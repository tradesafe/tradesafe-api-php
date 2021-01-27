<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Query;

trait Profile
{
    public function getProfile()
    {
        $gql = (new Query('apiProfile'));

        $gql->setSelectionSet([
            'id',
            'name',
            'token',
            (new Query('organizations'))
                ->setSelectionSet([
                    'id',
                    'name',
                    'verified',
                    'token'
                ]),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['apiProfile'];
    }
}
