<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Mutation;

trait Allocations
{
    public function allocationStartDelivery($id)
    {
        $gql = (new Mutation('allocationStartDelivery'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet([
            'id',
            'state'
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['allocationStartDelivery'];
    }

    public function allocationAcceptDelivery($id)
    {
        $gql = (new Mutation('allocationAcceptDelivery'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet([
            'id',
            'state'
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['allocationAcceptDelivery'];
    }
}
