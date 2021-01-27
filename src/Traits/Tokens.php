<?php

namespace TradeSafe\Api\Traits;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\RawObject;

trait Tokens
{
    public function createToken($user, $organization = null, $bankAccount = null)
    {
        $gql = (new Mutation('tokenCreate'));

        $input = sprintf('user: {
            givenName: "%s"
            familyName: "%s"
            email: "%s"
            mobile: "%s"
            idNumber: "%s"
            idType: %s
            idCountry: %s
        }', $user['givenName'], $user['familyName'], $user['email'], $user['mobile'], $user['idNumber'], $user['idType'], $user['idCountry']);

        if (isset($organization)) {
            $input .= sprintf('organization: {
                name: "%s"
                tradeName: "%s"
                type: %s
                registrationNumber: "%s"
                taxNumber: "%s"
            }', $organization['name'], $organization['tradeName'], $organization['type'], $organization['registrationNumber'], $organization['taxNumber']);
        }

        if (isset($bankAccount)) {
            $input .= sprintf('bankAccount: {
                bank: %s
                accountNumber: "%s"
                accountType: %s
            }', $bankAccount['bank'], $bankAccount['accountNumber'], $bankAccount['accountType']);
        }

        $input = '{' . $input . '}';

        $gql->setArguments(['input' => new RawObject($input)]);

        $gql->setSelectionSet([
            'id',
            'name',
            'reference',
            (new Query('user'))
                ->setSelectionSet([
                    'givenName',
                    'familyName',
                    'email',
                    'mobile',
                    'idNumber'
                ]),
            (new Query('organization'))
                ->setSelectionSet([
                    'name',
                    'tradeName',
                    'type',
                    'registration',
                    'taxNumber'
                ]),
            (new Query('bankAccount'))
                ->setSelectionSet([
                    'accountNumber',
                    'accountType',
                    'bank',
                    'branchCode',
                    'bankName'
                ]),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['tokenCreate'];
    }

    public function getTokens($page = 1, $first = 10)
    {
        $gql = (new Query('tokens'));

        $gql->setArguments(['page' => $page, 'first' => $first]);

        $gql->setSelectionSet([
            (new Query('data'))
                ->setSelectionSet([
                    'id',
                    'name',
                    'reference',
                    (new Query('user'))
                        ->setSelectionSet([
                            'givenName',
                            'familyName',
                            'email',
                            'mobile',
                            'idNumber'
                        ]),
                    (new Query('organization'))
                        ->setSelectionSet([
                            'name',
                            'tradeName',
                            'type',
                            'registration',
                            'taxNumber'
                        ]),
                    (new Query('bankAccount'))
                        ->setSelectionSet([
                            'accountNumber',
                            'accountType',
                            'bank',
                            'branchCode',
                            'bankName'
                        ]),
                ]),
            (new Query('paginatorInfo'))
                ->setSelectionSet([
                    'perPage',
                    'currentPage',
                    'hasMorePages',
                    'lastPage',
                    'firstItem',
                    'lastItem',
                    'total',
                    'count'
                ]),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['tokens'];
    }

    public function getToken($id)
    {
        $gql = (new Query('token'));

        $gql->setArguments(['id' => $id]);

        $gql->setSelectionSet([
            'id',
            'name',
            'reference',
            (new Query('user'))
                ->setSelectionSet([
                    'givenName',
                    'familyName',
                    'email',
                    'mobile',
                    'idNumber'
                ]),
            (new Query('organization'))
                ->setSelectionSet([
                    'name',
                    'tradeName',
                    'type',
                    'registration',
                    'taxNumber'
                ]),
            (new Query('bankAccount'))
                ->setSelectionSet([
                    'accountNumber',
                    'accountType',
                    'bank',
                    'branchCode',
                    'bankName'
                ]),
        ]);

        $gqlResponse = self::callApi($gql);

        return $gqlResponse['token'];
    }
}
