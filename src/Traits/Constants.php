<?php


namespace TradeSafe\Api\Traits;


use GraphQL\Query;

trait Constants
{
    public function getEnums(string $name)
    {
        $gql = (new Query('__type(name: "' . $name . '")'));

        $gql->setSelectionSet([
            (new Query('enumValues'))
                ->setSelectionSet([
                    'name',
                    'description'
                ])
        ]);

        $gqlResponse = self::callApi($gql, [], false);


        return $gqlResponse['__type']['enumValues'];
    }

    public function getIndustries()
    {
        $gql = (new Query('__type(name: "Industry")'));

        $gql->setSelectionSet([
            (new Query('enumValues'))
                ->setSelectionSet([
                    'name',
                    'description'
                ])
        ]);

        $gqlResponse = self::callApi($gql, [], false);

        return $gqlResponse['__type']['enumValues'];
    }

    public function getTransactionStates()
    {
        $gql = (new Query('__type(name: "State")'));

        $gql->setSelectionSet([
            (new Query('enumValues'))
                ->setSelectionSet([
                    'name',
                    'description'
                ])
        ]);

        $gqlResponse = self::callApi($gql, [], false);

        return $gqlResponse['__type']['enumValues'];
    }

    public function getFeeAllocations()
    {
        $gql = (new Query('__type(name: "FeeAllocation")'));

        $gql->setSelectionSet([
            (new Query('enumValues'))
                ->setSelectionSet([
                    'name',
                    'description'
                ])
        ]);

        $gqlResponse = self::callApi($gql, [], false);

        return $gqlResponse['__type']['enumValues'];
    }
}