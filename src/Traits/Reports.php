<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Query;

trait Reports {
    public function reportTransactionSummary($startDate = null, $endDate = null, $page = 1, $first = 10) {
        $gql = (new Query('reportTransactionSummary'));

        $args = ['page' => $page, 'first' => $first];

        if ($startDate) {
            $args['startDate'] = $startDate;
        }

        if ($endDate) {
            $args['endDate'] = $endDate;
        }

        $gql->setArguments($args);

        $query = [
            'id',
            'title',
            'state',
            'createdAt',
            'updatedAt',
            (new Query('parties'))
                ->setSelectionSet([
                    'id',
                    'role',
                    'owner',
                    (new Query('calculation'))
                        ->setSelectionSet([
                            'deposit',
                            'payout',
                            'processingFee',
                            'totalFee',
                        ]),
                ]),
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
            (new Query('allocations'))
                ->setSelectionSet([
                    (new Query('payments'))
                        ->setSelectionSet([
                            (new Query('account'))
                                ->setSelectionSet([
                                    'amount',
                                    'type',
                                    'status',
                                ]),
                        ]),
                ]),
            (new Query('payments'))
                ->setSelectionSet([
                    (new Query('account'))
                        ->setSelectionSet([
                            'amount',
                            'type',
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

        return $gqlResponse['reportTransactionSummary'];
    }

    public function reportTransactionSummaryDownload(string $type = 'csv', $startDate = null, $endDate = null) {
        switch ($type) {
            case "csv":
                $queryName = 'reportTransactionSummaryDownloadCsv';
                break;
            case "pdf":
                $queryName = 'reportTransactionSummaryDownloadPdf';
                break;
        }

        $gql = (new Query($queryName));

        $args = [];

        if ($startDate) {
            $args['startDate'] = $startDate;
        }

        if ($endDate) {
            $args['endDate'] = $endDate;
        }

        $gql->setArguments($args);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse[$queryName];
    }
}