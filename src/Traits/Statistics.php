<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Query;

trait Statistics
{
    public function getStatistics()
    {
        $gql = (new Query('statistics'));

        $gql->setSelectionSet([
            'id',
            'activeBalance',
            'activeTransactions',
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['statistics'];
    }
}
