<?php


namespace TradeSafe\Api;


use GuzzleHttp\Client as HttpClient;
use TradeSafe\Api\Traits\Allocations;
use TradeSafe\Api\Traits\Calculator;
use TradeSafe\Api\Traits\Profile;
use TradeSafe\Api\Traits\Statistics;
use TradeSafe\Api\Traits\Tokens;
use TradeSafe\Api\Traits\Transactions;

class Client
{
    use Allocations, Calculator, Profile, Statistics, Tokens, Transactions;
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
     * OAuth Domain Name.
     *
     * @var string
     */
    private $authDomain;

    /**
     * Base directory for GraphQL schema files.
     *
     * @var string
     */
    private $schemaBasePath;

    public function __construct($apiDomain = "api.tradesafe.co.za", $authDomain = 'auth.tradesafe.co.za')
    {
        $this->httpClient = new HttpClient([
            'base_uri' => sprintf('https://%s/', $apiDomain),
            'headers' => [
                'accept' => '*/*',
                'content-type' => 'application/json',
            ],
            'verify' => false
        ]);

        $this->authDomain = $authDomain;
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
            'urlAuthorize' => 'https://' . $this->authDomain . '/oauth/authorize',
            'urlAccessToken' => 'https://' . $this->authDomain . '/oauth/token',
            'urlResourceOwnerDetails' => 'https://' . $this->authDomain . '/oauth/resource',
        ]);

        $accessToken = $provider->getAccessToken('client_credentials');

        $this->token = $accessToken->getToken();

        return [
            'token' =>$this->token,
            'expires' => $accessToken->getExpires(),
        ];
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
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     */
    private function callApi($request)
    {
        $result = $this->httpClient->post('graphql', [
            'debug' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'json' => $request
        ]);

        $response = json_decode($result->getBody()->getContents(), true);

        if (isset($response['errors'])) {
            throw new \Exception($response['errors'][0]['message']);
        }

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
