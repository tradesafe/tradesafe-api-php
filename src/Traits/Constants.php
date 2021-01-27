<?php


namespace TradeSafe\Api\Traits;


use GraphQL\Query;

trait Constants
{
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
}