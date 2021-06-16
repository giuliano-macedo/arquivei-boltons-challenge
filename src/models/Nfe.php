<?php

final class Nfe
{
    private String $accessKey;
    private String $totalValue;
    function __construct(String $accessKey, String $totalValue)
    {
        $this->accessKey = $accessKey;
        $this->totalValue = $totalValue;
    }

    function getAccessKey(): String
    {
        return $this->accessKey;
    }
    function getTotalValue(): String
    {
        return $this->totalValue;
    }

    function toNamedArray(): mixed
    {
        return array('accessKey' => $this->accessKey, 'totalValue' => $this->totalValue);
    }
}
