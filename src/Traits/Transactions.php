<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Variable;

trait Transactions
{
    public $transactionQuery;
    public $paginatorInfo;

    public function transactionsInit()
    {
        $this->transactionQuery = [
            'id',
            'title',
            'description',
            'state',
            'industry',
            'feeAllocation',
            (new Query('parties'))
                ->setSelectionSet([
                    'id',
                    'name',
                    'role',
                    (new Query('details'))
                        ->setSelectionSet([
                            (new Query('user'))
                                ->setSelectionSet([
                                    'givenName',
                                    'familyName',
                                    'email',
                                ]),
                            (new Query('organization'))
                                ->setSelectionSet([
                                    'name',
                                    'tradeName',
                                    'type',
                                    'registration',
                                    'taxNumber',
                                ]),
                        ]),
                    (new Query('calculation'))
                        ->setSelectionSet([
                            'payout',
                            'totalFee',
                        ]),
                    'fee',
                    'feeType',
                    'feeAllocation'
                ]),
            (new Query('allocations'))
                ->setSelectionSet([
                    'id',
                    'title',
                    'description',
                    'value',
                    (new Query('amendments'))
                        ->setSelectionSet([
                            'id',
                            'value'
                        ]),
                ]),
            (new Query('deposits'))
                ->setSelectionSet([
                    'id',
                    'value',
                    'method',
                    'processed',
                    'paymentLink'
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
                ]),
            'createdAt',
            'updatedAt',
        ];

        $this->paginatorInfo = [
            'perPage',
            'hasMorePages',
            'currentPage',
            'lastPage',
            'firstItem',
            'lastItem',
            'total',
            'count'
        ];
    }

    /**
     * Get a list of transactions.
     *
     * @param int $page
     * @param int $first
     * @return mixed
     */
    public function getTransactions($page = 1, $first = 10)
    {
        $gql = (new Query('transactions'));

        $gql->setArguments(['page' => $page, 'first' => $first]);

        $gql->setSelectionSet([
            (new Query('data'))
                ->setSelectionSet($this->transactionQuery),
            (new Query('paginatorInfo'))
                ->setSelectionSet($this->paginatorInfo),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['transactions'];
    }

    /**
     * Get a transaction.
     *
     * @param $id
     * @return mixed
     */
    public function getTransaction($id)
    {
        $gql = (new Query('transaction'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet($this->transactionQuery);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['transaction'];
    }

    public function createTransaction($transactionData, $allocationData, $partyData)
    {
        $gql = (new Mutation('transactionCreate'));

        $allocationInput = '';
        $partyInput = '';

        foreach ($allocationData as $allocation) {
            if (isset($allocation['units'])
                && isset($allocation['unitCost'])) {
                $allocationInput .= sprintf('{
                    title: "%s",
                    description: "%s",
                    units: %s,
                    unitCost: %s,
                    daysToDeliver: %s,
                    daysToInspect: %s
                }', $allocation['title'],
                    $allocation['description'],
                    $allocation['units'] ?? 0,
                    $allocation['unitCost'] ?? 0,
                    $allocation['daysToDeliver'] ?? 14,
                    $allocation['daysToInspect'] ?? 7,
                );
            } else {
                $allocationInput .= sprintf('{
                    title: "%s",
                    description: "%s",
                    value: %s,
                    daysToDeliver: %s,
                    daysToInspect: %s
                }', $allocation['title'],
                    $allocation['description'],
                    $allocation['value'] ?? 0,
                    $allocation['daysToDeliver'] ?? 14,
                    $allocation['daysToInspect'] ?? 7,
                );
            }
        }

        foreach ($partyData as $party) {
            $partyInput .= sprintf('{
                token: "%s",
                email: "%s",
                role: %s,
                fee: %s,
                feeType: %s,
                feeAllocation: %s
            }', $party['token'] ?? null,
                $party['email'] ?? null,
                $party['role'] ?? null,
                $party['fee'] ?? 0,
                $party['feeType'] ?? 'PERCENT',
                $party['feeAllocation'] ?? 'SELLER',
            );
        }

        $input = sprintf('{
            title: "%s",
            description: "%s",
            industry: %s,
            currency: ZAR,
            feeAllocation: %s,
            workflow: %s,
            reference: "%s",
            privacy: %s,
            allocations: {
                create: [
                    %s
                ]
            },
            parties: {
                create: [
                    %s
                ]
            }
        }', $transactionData['title'],
            $transactionData['description'],
            $transactionData['industry'] ?? 'GENERAL_GOODS_SERVICES',
            $transactionData['feeAllocation'] ?? 'SELLER',
            $transactionData['workflow'] ?? 'STANDARD',
            $transactionData['reference'] ?? '',
            $transactionData['privacy'] ?? 'NONE',
            $allocationInput,
            $partyInput,
        );

        $gql->setArguments(['input' => new RawObject($input)]);

        $gql->setSelectionSet($this->transactionQuery);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['transactionCreate'];
    }

    /**
     * Update a transaction.
     *
     * @param $id
     * @param $args
     * @return mixed
     */
    public function updateTransaction($id, $args)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            'transactionUpdate',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['transaction'];
    }

    /**
     * Delete a transaction.
     *
     * @param $id
     * @return mixed
     */
    public function deleteTransaction($id)
    {
        $gql = (new Mutation('transactionDelete'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet([
            'id',
            'deletedAt'
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['transactionDelete'];
    }

    /**
     * Create Transaction Deposit
     * @param $id
     * @param $method
     * @param null $redirects
     * @return mixed
     */
    public function createTransactionDeposit($id, $method, $redirects)
    {
        $gql = (new Mutation('transactionDeposit'));

        $gql->setVariables([
            new Variable('id', 'ID', true),
            new Variable('method', 'DepositMethod', true),
            new Variable('redirects', 'TransactionDepositRedirects', true)
        ]);

        $variables = [
            'id' => $id,
            'method' => $method,
            'redirects' => $redirects
        ];

        $gql->setArguments(['id' => '$id', 'method' => '$method', 'redirects' => '$redirects']);

        $gql->setSelectionSet([
            'id',
            'value',
            'processed',
            'paymentLink',
            (new Query('redirects'))
                ->setSelectionSet([
                    'success',
                    'failure',
                    'cancel'
                ]),
        ]);

        $gqlResponse = self::callApi($gql, $variables);

        return $gqlResponse['transactionDeposit'];
    }

    /**
     * Get Transaction Deposit Link
     * @param $id
     * @param $method
     * @param null $redirects
     * @return mixed
     */
    public function getTransactionDepositLink($id)
    {
        $gql = (new Query('transactionDepositLink'));

        $gql->setVariables([
            new Variable('id', 'ID', true),
        ]);

        $variables = [
            'id' => $id,
        ];

        $gql->setArguments(['id' => '$id']);

        $gqlResponse = self::callApi($gql, $variables);

        return $gqlResponse['transactionDepositLink'];
    }
}
