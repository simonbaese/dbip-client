<?php

namespace Scullwm\DbIpClient;

use Http\Client\HttpClient;
use Scullwm\DbIpClient\IpDetails;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Scullwm\DbIpClient\Exception\QuotaExceededException;
use Scullwm\DbIpClient\Exception\InvalidCredentialsException;
use Scullwm\DbIpClient\Exception\InvalidServerResponseException;

class Client
{
    private ClientInterface $client;

    private RequestFactoryInterface $messageFactory;

    private string $token;

    private const API_ENDPOINT_V2 = 'http://api.db-ip.com/v2/%s/%s';

    public function __construct(string $token, ClientInterface $client = null, RequestFactoryInterface $factory = null)
    {
        $this->token = $token;
        $this->client = $client ?: Psr18ClientDiscovery::find();
        $this->messageFactory = $factory ?: Psr17FactoryDiscovery::findRequestFactory();
    }

    public function getIpDetails(string $ip)
    {
        $request = $this->getRequest(sprintf(self::API_ENDPOINT_V2, $this->token, $ip));

        return $this->getParsedResponse($request);
    }

    protected function getParsedResponse(RequestInterface $request): IpDetails
    {
        $response = $this->getHttpClient()->sendRequest($request);

        $statusCode = $response->getStatusCode();
        if (401 === $statusCode || 403 === $statusCode) {
            throw new InvalidCredentialsException();
        } elseif (429 === $statusCode) {
            throw new QuotaExceededException();
        } elseif ($statusCode >= 300) {
            throw InvalidServerResponseException::create((string) $request->getUri(), $statusCode);
        }

        $body = (string) $response->getBody();
        if ('' === $body) {
            throw InvalidServerResponseException::emptyResponse((string) $request->getUri());
        }

        if (($content = json_decode($body, true)) && json_last_error() !== JSON_ERROR_NONE) {
            throw InvalidServerResponseException::invalidJson((string) $request->getUri(), $body);
        }

        return IpDetails::new($content);
    }

    protected function getRequest(string $url): RequestInterface
    {
        return $this->getMessageFactory()->createRequest('GET', $url);
    }

    protected function getHttpClient(): HttpClient
    {
        return $this->client;
    }

    protected function getMessageFactory(): RequestFactoryInterface
    {
        return $this->messageFactory;
    }
}