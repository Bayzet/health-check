<?php

namespace bayzet\HealthCheckBundle\Entity;

class CommonHealthData implements HealthDataInterface
{
    private int $status;
    private array $additionalInfo = [];

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function setAdditionalInfo(array $additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getAdditionalInfo(): array
    {
        return $this->additionalInfo;
    }
}