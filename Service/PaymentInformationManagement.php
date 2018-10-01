<?php

namespace CodiLuck\AuthorizenetVisa\Service;

use CodiLuck\AuthorizenetVisa\Api\PaymentInformationManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Psr\Log\LoggerInterface;

/**
 * Class PaymentInformationManagement
 * @package CodiLuck\AuthorizenetVisa\Service
 */
class PaymentInformationManagement implements PaymentInformationManagementInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CommandPoolInterface
     */
    protected $commandPool;

    /**
     * @var PaymentDataObjectFactory
     */
    protected $paymentDataObjectFactory;
    /**
     * PaymentInformationManagement constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param CommandPoolInterface $commandPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Session $session,
        CommandPoolInterface $commandPool,
        PaymentDataObjectFactory $paymentDataObjectFactory
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->commandPool = $commandPool;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function savePaymentInformation($response)
    {
        $order = $this->session->getLastRealOrder();
        if (!$order->getId()) {
            throw new LocalizedException(__('Order does not exist.'));
        }

        $arguments = [
            'response' => json_decode($response, true),
            'payment' => $this->paymentDataObjectFactory->create($order->getPayment())
        ];

        try {
            $this->commandPool->get('visa_complete')->execute($arguments);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $e
            );
        }

        return true;
    }
}