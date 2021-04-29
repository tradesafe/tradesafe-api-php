<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Query;

trait Reports {
    public function reportTransactionSummary($startDate = null, $endDate = null, $page = 1, $first = 10) {
        $gql = (new Query('reportTransactionSummary'));

        $gql->setArguments(['page' => $page, 'first' => $first]);

        $query = [
            'id',
            'title',
            'state',
            'createdAt',
            'updatedAt',
            (new Query('calculation'))
                ->setSelectionSet([
                    'baseValue',
                    'totalValue',
                    'totalDeposits',
                    'processingFeePercentage',
                    'processingFeeValue',
                    'processingFeeVat',
                    'processingFeeTotal',
                ]),
            (new Query('payments'))
                ->setSelectionSet([
                    (new Query('account'))
                        ->setSelectionSet([
                            'amount',
                            'status',
                        ]),
                ]),
        ];

        $paginatorInfo = [
            'perPage',
            'hasMorePages',
            'currentPage',
            'lastPage',
            'firstItem',
            'lastItem',
            'total',
            'count'
        ];

        $gql->setSelectionSet([
            (new Query('data'))
                ->setSelectionSet($query),
            (new Query('paginatorInfo'))
                ->setSelectionSet($paginatorInfo),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['transactions'];
    }
}