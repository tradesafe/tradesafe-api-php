<?php


namespace TradeSafe\Api;


use GuzzleHttp\Client as HttpClient;

class Client
{
    /**
     * Application Client ID.
     *
     * @var string
     */
    private $clientId;

    /**
     * Application Client Secret.
     *
     * @var string
     */
    private $clientSecret;

    /**
     * Application Client Redirect Url.
     *
     * @var string
     */
    private $clientRedirectUri;

    /**
     * Json Web Token for auth.
     *
     * @var string
     */
    private $token;

    /**
     * HTTP Client.
     *
     * @var string
     */
    private $httpClient;

    /**
     * Base directory for GraphQL schema files.
     *
     * @var string
     */
    private $schemaBasePath;

    public function __construct($domain = "api.tradesafe.co.za")
    {
        $this->httpClient = new HttpClient([
            'base_uri' => sprintf('https://%s/', $domain),
            'headers' => [
                'accept' => '*/*',
                'content-type' => 'application/json',
            ],
            'verify' => false
        ]);

        $this->schemaBasePath = __DIR__ . '/../graphql/';
    }

    /**
     * Configure the API Client.
     *
     * @param string $clientId Application Client ID
     * @param string $clientSecret Application Client Secret
     * @param string $clientRedirectUri
     */
    public function configure(string $clientId, string $clientSecret, string $clientRedirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->clientRedirectUri = $clientRedirectUri;
    }

    /**
     * Create oAuth2 token for requests.
     */
    public function generateAuthToken()
    {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->clientRedirectUri,
            'urlAuthorize' => 'https://auth.tradesafe.co.za/oauth/authorize',
            'urlAccessToken' => 'https://auth.tradesafe.co.za/oauth/token',
            'urlResourceOwnerDetails' => 'https://auth.tradesafe.co.za/oauth/resource',
        ]);

        $this->token = $provider->getAccessToken('client_credentials');
    }

    /**
     * Set oAuth2 token for requests.
     * @param $token
     */
    public function setAuthToken($token)
    {
        $this->token = $token;
    }

    /**
     * Send request to the API.
     *
     * @param $request
     * @return mixed
     */
    private function callApi($request)
    {
        $result = $this->httpClient->post('graphql', [
            'debug' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'body' => json_encode($request)
        ]);

        return json_decode($result->getBody()->getContents(), true);
    }

    /**
     * Create the GraphQL request using the provided schema.
     *
     * @param $schema
     * @param $operation
     * @param $variables
     * @return array
     */
    private function createGraphQLRequest($schema, $operation, $variables = [])
    {
        $query = file_get_contents($this->schemaBasePath . $schema);

        return [
            'operationName' => $operation,
            'query' => $query,
            'variables' => $variables
        ];
    }

    public function getStatistics()
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'queries/statistics.graphql',
            'statistics'
        ));

        return $apiResponse['data']['statistics'];
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
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'queries/transactions.graphql',
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
            'queries/transactions.graphql',
            'transaction',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['transaction'];
    }

    public function createTransaction(Transaction $transaction)
    {
        $operation = 'transactionCreate';
        $createTransactionInput = $transaction->toArray();

        $parties = $createTransactionInput['parties'];
        $createTransactionInput['parties'] = [
            'create' => $parties
        ];

        $allocations = $createTransactionInput['allocations'];
        $createTransactionInput['allocations'] = [
            'create' => $allocations
        ];

        $apiResponse = self::callApi(self::createGraphQLRequest(
            'mutations/transactions.graphql',
            $operation,
            [
                'input' => $createTransactionInput
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
            'mutations/transactions.graphql',
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
            'mutations/transactions.graphql',
            'transactionDelete',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['transaction'];
    }

    public function getTokens($page = 1, $first = 10)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'queries/tokens.graphql',
            'tokens',
            [
                'first' => $first,
                'page' => $page
            ]
        ));

        return $apiResponse['data']['tokens'];
    }

    public function getToken($id)
    {
        $apiResponse = self::callApi(self::createGraphQLRequest(
            'queries/tokens.graphql',
            'token',
            [
                'id' => $id
            ]
        ));

        return $apiResponse['data']['token'];
    }
}
