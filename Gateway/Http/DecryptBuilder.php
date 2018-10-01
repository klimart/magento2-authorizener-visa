<?php

namespace CodiLuck\AuthorizenetVisa\Gateway\Http;

use CodiLuck\Authorizenet\Gateway\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DecryptBuilder implements BuilderInterface
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $response = $buildSubject['response'];

        $dataValue = $response['encPaymentData'] ?? null;
        $dataKey = $response['encKey'] ?? null;
        $transactionId = $response['callid'] ?? $response['callId'] ?? null;
        return [
            'decryptPaymentDataRequest' => [
                'merchantAuthentication' => [
                    'name' => $this->config->getApiLoginId(),
                    'transactionKey' => $this->config->getTransactionKey()
                ],
                'opaqueData' => [
                    'dataDescriptor' => 'COMMON.VCO.ONLINE.PAYMENT',
                    'dataValue' => $dataValue,
                    'dataKey' => $dataKey
                ],
                'callId' => $transactionId
            ]
        ];
    }


}