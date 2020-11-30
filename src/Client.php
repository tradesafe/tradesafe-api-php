<?php


namespace TradeSafe\Api;


use GuzzleHttp\Client as HttpClient;
use TradeSafe\Api\Traits\Profile;
use TradeSafe\Api\Traits\Statistics;
use TradeSafe\Api\Traits\Tokens;
use TradeSafe\Api\Traits\Transactions;

class Client
{
    use Transactions, Tokens, Statistics, Profile;
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
        $domain = 'auth.tradesafe.co.za';

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->clientRedirectUri,
            'urlAuthorize' => 'https://' . $domain . '/oauth/authorize',
            'urlAccessToken' => 'https://' . $domain . '/oauth/token',
            'urlResourceOwnerDetails' => 'https://' . $domain . '/oauth/resource',
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

        $response = json_decode($result->getBody()->getContents(), true);

        return $response;
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
}
