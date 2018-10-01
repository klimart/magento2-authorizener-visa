<?php

namespace CodiLuck\AuthorizenetVisa\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;

/**
 * Class PaymentDetailsHandler
 * @package CodiLuck\AuthorizenetVisa\Gateway\Response
 */
class UpdateOrderHandler implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UpdateOrderHandler constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderSender $orderSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderSender $orderSender,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var Payment $payment */
        $payment = $handlingSubject['payment']->getPayment();

        $baseTotalDue = $payment->getOrder()->getBaseTotalDue();
        $payment->registerCaptureNotification($baseTotalDue);

        if (!$payment->getOrder()->getEmailSent()) {
            try {
                /* Send order email */
                $this->orderSender->send($payment->getOrder());
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }

        $this->orderRepository->save($payment->getOrder());
    }
}