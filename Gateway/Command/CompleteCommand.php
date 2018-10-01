<?php

namespace CodiLuck\AuthorizenetVisa\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\ErrorMapper\ErrorMessageMapperInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CompleteCommand
 * @package CodiLuck\AuthorizenetVisa\Gateway\Command
 */
class CompleteCommand implements CommandInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var BuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    protected $transferFactory;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ErrorMessageMapperInterface
     */
    protected $errorMessageMapper;

    /**
     * CompleteCommand constructor.
     * @param LoggerInterface $logger
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param HandlerInterface|null $handler
     * @param ValidatorInterface|null $validator
     * @param ErrorMessageMapperInterface|null $errorMessageMapper
     */
    public function __construct(
        LoggerInterface $logger,
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        HandlerInterface $handler = null,
        ValidatorInterface $validator = null,
        ErrorMessageMapperInterface $errorMessageMapper = null
    ) {
        $this->logger = $logger;
        $this->requestBuilder = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->handler = $handler;
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $transferO = $this->transferFactory->create(
            $this->requestBuilder->build($commandSubject)
        );

        $response = $this->client->placeRequest($transferO);
        if ($this->validator !== null) {
            $result = $this->validator->validate(
                array_merge($commandSubject, ['response' => $response])
            );
            if (!$result->isValid()) {
                $this->processErrors($result);
            }

            if ($this->handler) {
                $this->handler->handle(
                    $commandSubject,
                    $response
                );
            }
        }
    }

    /**
     * @param ResultInterface $result
     * @throws CommandException
     */
    protected function processErrors(ResultInterface $result)
    {
        $messages = [];
        foreach ($result->getFailsDescription() as $failPhrase) {
            $message = (string) $failPhrase;

            if ($this->errorMessageMapper !== null) {
                $mapped = (string) $this->errorMessageMapper->getMessage($message);
                if (!empty($mapped)) {
                    $messages[] = $mapped;
                    $message = $mapped;
                }
            }
            $this->logger->critical('Payment Error: ' . $message);
        }

        throw new CommandException(
            !empty($messages)
                ? __(implode(PHP_EOL, $messages))
                : __('Transaction has been declined. Please try again later.')
        );
    }
}