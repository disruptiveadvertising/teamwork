<?php  namespace Rossedman\Teamwork;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Request;
use Rossedman\Teamwork\Contracts\RequestableInterface;

class Client implements RequestableInterface {

    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var GuzzleHttp\Request
     */
    protected $request;

    /**
     * @var GuzzleHttp\Response
     */
    protected $response;

    /**
     * API Key
     *
     * The custom API key provided by Teamwork
     *
     * @var string
     */
    protected $key;

    /**
     * URL
     *
     * The URL that is set to query the Teamwork API.
     * This is the account URL used to access the project
     * management system. This is passed in on construct.
     *
     * @var string
     */
    protected $url;

    /**
     * Currently this package doesn't support XML
     * but overtime this would be part of that support
     *
     * @var string
     */
    protected $dataFormat = 'json';

    /**
     * @param Guzzle $client
     * @param        $key
     * @param        $url
     */
    public function __construct(Guzzle $client, $key, $url)
    {
        $this->client = $client;
        $this->key = $key;
        $this->url = $url;
    }

    /**
     * Get
     *
     * @param $endpoint
     *
     * @return Client
     */
    public function get($endpoint, $query = null)
    {
        $this->buildRequest($endpoint, 'GET', $query, $query);

        return $this;
    }

    /**
     * Post
     *
     * @param $endpoint
     * @param $data
     *
     * @return Client
     */
    public function post($endpoint, $data)
    {
        return $this->buildRequest($endpoint, 'POST', $data);
    }

    /**
     * Put
     *
     * @param $endpoint
     * @param $data
     *
     * @return Client
     */
    public function put($endpoint, $data)
    {
        return $this->buildRequest($endpoint, 'PUT', $data);
    }

    /**
     * Delete
     *
     * @param $endpoint
     *
     * @return Client
     * @internal param $data
     *
     */
    public function delete($endpoint)
    {
        return $this->buildRequest($endpoint, 'DELETE');
    }

    /**
     * Build Request
     *
     * build up request including authentication, body,
     * and string queries if necessary. This is where the bulk
     * of the data is build up to connect to Teamwork with.
     *
     * @param        $endpoint
     * @param string $action
     * @param array  $params
     *
     * @return $this
     */
    public function buildRequest($endpoint, $action, $params = null, $query = null)
    {
        if (!is_null($params) && gettype($params) == 'array')
        {
            $params = json_encode($params);
        }
        else
            $params = '';

        $this->request = new Request(
            $action,
            $this->buildUrl($endpoint, $query),
            ['Authorization' => "Basic " . base64_encode($this->key . ":xxxx")],
            $params
        );

        return $this;
    }

    /**
     * Response
     *
     * this send the request from the built response and
     * returns the response payload as a PHP array
     */
    public function response($json = true)
    {
        $this->response = $this->client->send($this->request);
        return json_decode($this->response->getBody()->getContents());
    }

    /**
     * Response As JSON
     *
     * this send the request from the built response and
     * returns the response as a JSON payload
     */
    public function responseAsJson()
    {
        $this->response = $this->client->send($this->request);
        return $this->response->getBody()->getContents();
    }

    /**
     * Build Url
     *
     * builds the url to make the request to Teamwork with
     * and passes it into Guzzle. Also checks if trailing slash
     * is present.
     *
     * @param $endpoint
     *
     * @return string
     */
    public function buildUrl($endpoint, $query = [])
    {
        if (filter_var($endpoint, FILTER_VALIDATE_URL))
        {
            return $endpoint . '.' . $this->dataFormat;
        }

        if (substr($this->url, -1) != '/')
        {
            $this->url = $this->url . '/';
        }

        $query_string = '';
        if(!is_null($query) && count($query) > 0)
        {
            $query_string = '?' . http_build_query($query);
        }

        return $this->url . $endpoint . '.' . $this->dataFormat . $query_string;
    }

    /**
     * Build Query String
     *
     * if a query string is needed it will be built up
     * and added to the request. This is only used in certain
     * GET requests
     *
     * @param $query
     */
    public function buildQuery($query)
    {
        $q = $this->request->getUri()->getQuery();
        foreach ($query as $key => $value)
        {
            $q[$key] = $value;
        }
    }

    /**
     * Get Request
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getHeadersFromResponse(){
        return $this->response->getHeaders();
    }
}
