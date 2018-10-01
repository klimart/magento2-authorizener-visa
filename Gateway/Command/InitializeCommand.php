<?php

namespace CodiLuck\AuthorizenetVisa\Gateway\Command;

use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

/**
 * Class InitializeCommand
 * @package CodiLuck\AuthorizenetVisa\Gateway\Command
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        /** @var DataObject $stateObject */
        $stateObject = $commandSubject['stateObject'];

        /** @var Payment $payment */
        $payment = $commandSubject['payment']->getPayment();

        $payment->setAmountAuthorized($payment->getOrder()->getTotalDue());
        $payment->setBaseAmountAuthorized($payment->getOrder()->getBaseTotalDue());
        $payment->getOrder()->getCanSendNewEmailFlag(false);
        $payment->getOrder()->setCustomerNoteNotify(false);

        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData('is_notified', false);
    }
}