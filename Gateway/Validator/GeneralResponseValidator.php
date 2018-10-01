<?php

namespace CodiLuck\AuthorizenetVisa\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

/**
 * Class GeneralResponseValidator
 * @package CodiLuck\AuthorizenetVisa\Gateway\Validator
 */
class GeneralResponseValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];

        $isValid = true;
        $errorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($response);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                $errorMessages = array_merge($errorMessages, $validationResult[1]);
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }

    /**
     * @return array
     */
    protected function getResponseValidators()
    {
        return [
            function ($response) {
                return [
                    isset($response['messages']['resultCode']) && 'Ok' === $response['messages']['resultCode'],
                    [$response['messages']['message'][0]['text'] ?? __('Authorize.NET error response')]
                ];
            }
        ];
    }
}