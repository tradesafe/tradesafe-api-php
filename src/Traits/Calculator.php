<?php

namespace TradeSafe\Api\Traits;

trait Calculator
{
    public function getCalculation($args)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'calculator.graphql',
            'calculator',
            [
                'feeAllocation' => $args['feeAllocation'],
                'industry' => $args['industry'],
                'value' => $args['value'],
            ]
        ));

        return $apiResponse['data']['calculator'];
    }
}
