<?php

namespace  CodiLuck\AuthorizenetVisa\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;

/**
 * Class Config
 * @package CodiLuck\AuthorizenetVisa\Gateway
 */
class Config
{
    /**
     * @var ValueHandlerPoolInterface
     */
    protected $valueHandlerPool;

    /**
     * Config constructor.
     * @param ValueHandlerPoolInterface $valueHandlerPool
     */
    public function __construct(
        ValueHandlerPoolInterface $valueHandlerPool
    ) {
        $this->valueHandlerPool = $valueHandlerPool;
    }

    /**
     * @return bool
     */
    public function isSandbox()
    {
        return (bool) $this->getValue('is_sandbox');
    }

    /**
     * @return string
     */
    public function getSdkUrl()
    {
        if ($this->isSandbox()) {
            return (string) $this->getValue('sdk_url_sandbox');
        }

        return (string) $this->getValue('sdk_url');
    }

    /**
     * @return string
     */
    public function getCheckoutButtonSrc()
    {
        if ($this->isSandbox()) {
            return (string) $this->getValue('checkout_button_src_sandbox');
        }

        return (string) $this->getValue('checkout_button_src');
    }

    /**
     * @return string
     */
    public function getPaymentCardSrc()
    {
        return (string) $this->getValue('payment_card_src');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getValue('title');
    }

    /**
     * @return string
     */
    public function getMerchantSourceId()
    {
        return (string) $this->getValue('merchant_source_id');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        if ($this->isSandbox()) {
            return (string) $this->getValue('api_key_sandbox');
        }

        return (string) $this->getValue('api_key');
    }

    /**
     * @return string
     */
    public function getReviewMessage()
    {
        return (string) $this->getValue('review_message');
    }

    /**
     * @return string
     */
    public function getButtonActionTitle()
    {
        return (string) $this->getValue('button_action_title');
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return (string) $this->getValue('display_name');
    }

    /**
     * @return bool
     */
    public function isCollectShipping()
    {
        return (bool) $this->getValue('is_collect_shipping');
    }

    /**
     * @param $field
     * @return mixed|null
     */
    protected function getValue($field)
    {
        try {
            $handler = $this->valueHandlerPool->get($field);
            return $handler->handle(['field' => $field]);

        } catch (NotFoundException $exception) {
            return null;
        }
    }
}