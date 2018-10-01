<?php

namespace CodiLuck\AuthorizenetVisa\Api;

/**
 * Interface PaymentInformationManagementInterface
 * @package CodiLuck\AuthorizenetVisa\Api
 */
interface PaymentInformationManagementInterface
{
    /**
     * @param string $response
     * @return boolean
     */
    public function savePaymentInformation($response);
}