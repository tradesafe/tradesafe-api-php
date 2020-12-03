<?php

namespace TradeSafe\Api\Traits;

use TradeSafe\Api\Transaction;

trait Transactions {
    /**
     * Get a list of transactions.
     *
     * @param int $page
     * @param int $first
     * @return mixed
     */
    public function getTransactions($page = 1, $first = 10)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            'transactions',
            [
                'first' => $first,
                'page' => $page
            ]
        ));

        return $apiResponse['data']['transactions'];
    }

    /**
     * Get a transaction.
     *
     * @param $id
     * @return mixed
     */
    public function getTransaction($id)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            'transaction',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['transaction'];
    }

    public function createTransaction($transactionData)
    {
        $operation = 'transactionCreate';

        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            $operation,
            [
                'title' => $transactionData['title'],
                'description' => $transactionData['description'],
                'industry' => $transactionData['industry'],
                'workflow' => $transactionData['workflow'] ?? 'STANDARD',
                'value' => $transactionData['value'],
                'buyerToken' => $transactionData['buyerToken'],
                'sellerToken' => $transactionData['sellerToken']
            ]
        ));

        return $apiResponse['data'][$operation];
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
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            'transactionDelete',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['transaction'];
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
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'transactions.graphql',
            'transactionDeposit',
            [
                'id' => $id,
                'method' => $method,
                'successUrl' => $redirects['success'],
                'failureUrl' => $redirects['failure'],
                'cancelUrl' => $redirects['cancel'],
            ]
        ));

        return $apiResponse['data']['transactionDeposit'];
    }
}
