<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\RawObject;

trait Calculator
{
    public function getCalculation($value, $feeAllocation, $industry = 'GENERAL_GOODS_SERVICES')
    {
        $gql = (new Mutation('calculator'));

        $input = sprintf('{feeAllocation: %s, industry: %s, allocations: [{value: %s}], parties: [{role: BUYER}, {role: SELLER}]}', $feeAllocation, $industry, $value);

        $gql->setArguments(['input' => new RawObject($input)]);

        $gql->setSelectionSet([
            'baseValue',
            'totalValue',
            'totalDeposits',
            'processingFeePercentage',
            'processingFeeValue',
            'processingFeeVat',
            'processingFeeTotal',
            (new Query('gatewayProcessingFees'))
                ->setSelectionSet([
                    (new Query('manualEft'))
                        ->setSelectionSet([
                            'processingFee',
                            'totalValue'
                        ]),
                    (new Query('ecentric'))
                        ->setSelectionSet([
                            'processingFee',
                            'totalValue'
                        ]),
                    (new Query('ozow'))
                        ->setSelectionSet([
                            'processingFee',
                            'totalValue'
                        ]),
                    (new Query('snapscan'))
                        ->setSelectionSet([
                            'processingFee',
                            'totalValue'
                        ]),
                ]),
            (new Query('parties'))
                ->setSelectionSet([
                    'role',
                    'deposit',
                    'payout',
                    'commission',
                    'processingFee',
                    'agentFee',
                    'beneficiaryFee',
                    'totalFee'
                ]),
            (new Query('allocations'))
                ->setSelectionSet([
                    'value',
                    'units',
                    'unitCost',
                    'refund',
                    'payout',
                    'fee',
                    'processingFee',
                ]),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['calculator'];
    }
}
