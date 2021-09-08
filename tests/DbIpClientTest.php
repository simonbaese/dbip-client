<?php

declare(strict_types=1);

use Scullwm\DbIpClient\Client as TestedClient;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

final class DbIpClientTest extends TestCase
{
    /**
     * @dataProvider provideIpListing
     */
    public function testGettingIp(string $ip)
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . sprintf('/Fixtures/%s.json', $ip)));
        $client->setDefaultResponse($response);
        $dbipClient = new TestedClient('faketoken', $client);

        $ipDetails = $dbipClient->getIpDetails($ip);

        $this->assertEquals($ipDetails->getIpAddress(), $ip);
    }

    public function provideIpListing()
    {
        return [
            ['8.8.8.8'],
            ['12.25.8.200'],
            ['192.168.1.1'],
            ['2.2.2.2'],
        ];
    }

    public function testFullDetail()
    {
        $client = new Client();

        $response = $this->getResponse(file_get_contents(__DIR__ . '/Fixtures/12.25.8.200.json'));
        $client->setDefaultResponse($response);
        $dbipClient = new TestedClient('faketoken', $client);

        $ipDetails = $dbipClient->getIpDetails('12.25.8.200');

        $this->assertEquals($ipDetails->isRisky(), false);

        $this->assertEquals($ipDetails->getContinentCode(), 'NA');
        $this->assertEquals($ipDetails->getContinentName(), 'North America');
        $this->assertEquals($ipDetails->getCountryCode(), 'US');
        $this->assertEquals($ipDetails->getCountryName(), 'États-Unis');
        $this->assertEquals($ipDetails->getStateProv(), 'Caroline du Sud');
        $this->assertEquals($ipDetails->getCity(), 'North Charleston');
        $this->assertEquals($ipDetails->getThreatLevel(), 'low');
        $this->assertEquals($ipDetails->getIsp(), 'AT&T Services');
    }


    private function getResponse(string $content, int $statusCode = 200): ResponseInterface
    {
        $body = $this->createMock(StreamInterface::class);
        $body
            ->expects($this->once())
            ->method('__toString')
            ->willReturn($content);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        return $response;
    }
}
