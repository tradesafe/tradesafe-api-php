<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Mutation;
use GraphQL\RawObject;

trait Amendments
{
    public function createAmendment($allocationId, $amendmentData)
    {
        $gql = (new Mutation('amendmentCreate'));

        $input = '';

        foreach ($amendmentData as $key => $value) {
            $input .= sprintf("%s: %s\n", $key, $value);
        }

        $args = [
            'allocationId' => $allocationId,
            'input' => new RawObject('{' . $input . '}')
        ];

        $gql->setArguments($args);

        $gql->setSelectionSet([
            'id',
            'state',
            'value',
            'units',
            'unitCost',
            'daysToDeliver',
            'deliverBy',
            'daysToInspect',
            'inspectBy',
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['amendmentCreate'];
    }

    public function acceptAmendment($id)
    {
        $gql = (new Mutation('amendmentAccept'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet([
            'id',
            'state',
            'value',
            'units',
            'unitCost',
            'daysToDeliver',
            'deliverBy',
            'daysToInspect',
            'inspectBy',
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['amendmentAccept'];
    }

    public function cancelAmendment($id, $comment = null)
    {
        $gql = (new Mutation('amendmentCancel'));

        $args = ['id' => $id];

        if ($comment) {
            $args['comment'] = $comment;
        }

        $gql->setArguments($args);

        $gql->setSelectionSet([
            'id',
            'state',
            'value',
            'units',
            'unitCost',
            'daysToDeliver',
            'deliverBy',
            'daysToInspect',
            'inspectBy',
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['amendmentCancel'];
    }

    public function declineAmendment($id, $comment = null)
    {
        $gql = (new Mutation('amendmentDecline'));

        $args = ['id' => $id];

        if ($comment) {
            $args['comment'] = $comment;
        }

        $gql->setArguments($args);

        $gql->setSelectionSet([
            'id',
            'state',
            'value',
            'units',
            'unitCost',
            'daysToDeliver',
            'deliverBy',
            'daysToInspect',
            'inspectBy',
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['amendmentDecline'];
    }
}
