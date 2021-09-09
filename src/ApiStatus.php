<?php

namespace Scullwm\DbIpClient;

class ApiStatus
{
    private string $apiKey;
    private int $queriesPerDay;
    private int $queriesLeft;
    private string $status;

    public function __construct(string $apiKey, int $queriesPerDay, int $queriesLeft, string $status)
    {
        $this->apiKey = $apiKey;
        $this->queriesPerDay = $queriesPerDay;
        $this->queriesLeft = $queriesLeft;
        $this->status = $status;
    }

    public static function new(array $data, string $defaultApiToken = ''): self
    {
        return new self(
            (isset($data['apiKey'])) ? $data['apiKey'] : $defaultApiToken,
            (isset($data['queriesPerDay'])) ? $data['queriesPerDay'] : 0,
            (isset($data['queriesLeft'])) ? $data['queriesLeft'] : 0,
            (isset($data['status'])) ? $data['status'] : 'unknow'
        );
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getQueriesPerDay(): int
    {
        return $this->queriesPerDay;
    }

    public function getQueriesLeft(): int
    {
        return $this->queriesLeft;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
