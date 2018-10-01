<?php

namespace CodiLuck\AuthorizenetVisa\Ui;

use CodiLuck\AuthorizenetVisa\Gateway\Config;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Model\Quote;

/**
 * Class ConfigProvider
 * @package CodiLuck\AuthorizenetVisa\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Repository $repository
     * @param RequestInterface $request
     * @param Session $session
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Repository $repository,
        RequestInterface $request,
        Session $session
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->request = $request;
        $this->assetRepo = $repository;
        $this->quote = $session->getQuote();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $visaCheckoutButtonSrc = $this->config->getCheckoutButtonSrc() . '?cardGrands=VISA,MASTERCARD,DISCOVER,AMEX';
        return [
            'payment' => [
                'codiluck_authorizenet' => [
                    'title' => $this->config->getTitle(),
                    'sdkUrl' => $this->config->getSdkUrl(),
                    'paymentCardSrc' => $this->config->getPaymentCardSrc(),
                    'visaCheckoutButtonSrc' => $visaCheckoutButtonSrc,
                    'visaCheckoutInitialSettings' => [
                        'apikey' => $this->config->getApiKey(),
                        'sourceId' => $this->config->getMerchantSourceId(),
                        'settings' => [
                            'locale' => 'en_US',
                            'countryCode' => 'US',
                            'displayName' => $this->config->getDisplayName(),
                            'logoUrl' => $this->getLogoUrl(),
                            'websiteUrl' => $this->url->getBaseUrl(),
                            'shipping' => [
                                'acceptedRegions' => ['US', 'CA'],
                                'collectShipping' => $this->config->isCollectShipping()
                            ],
                            'review' => [
                                'message' => $this->config->getReviewMessage(),
                                'buttonAction' => $this->config->getButtonActionTitle()
                            ],
                            'dataLevel' => 'SUMMARY'
                        ],
                        'paymentRequest' => [
                            'merchantRequestId' => $this->quote->getId(),
                            'currencyCode' => $this->quote->getBaseCurrencyCode(),
                            'subtotal' => $this->quote->getBaseSubtotal(),
                            'total' => $this->quote->getBaseGrandTotal()
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $fileId
     * @return string
     */
    public function getViewFileUrl($fileId)
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()]);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return '';
        }
    }

    /**
     * @return mixed
     */
    protected function getLogoUrl()
    {
        return $this->getViewFileUrl('images/logo.svg');
    }
}