<?php

namespace App\Utils\Service;

use App\Entity\Enum\PaymentProcessor;
use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use Exception;

/**
 * Сервис для проведения оплат
 */
readonly class PaymentService
{
    public function __construct(
        private PaypalPaymentProcessor $paypalPaymentProcessor,
        private StripePaymentProcessor $stripePaymentProcessor,
    )
    {
    }


    /**
     * @param PaymentProcessor $paymentProcessor
     * @param float $price
     * @return void
     *
     * @throws Exception
     */
    public function pay(PaymentProcessor $paymentProcessor, float $price): void
    {
        switch ($paymentProcessor->value) {
            case PaymentProcessor::PAYPAL->value:
                $this->paypalPaymentProcessor->pay($price);
                break;
            case PaymentProcessor::STRIPE->value:
                if (!$this->stripePaymentProcessor->processPayment($price)) {
                    throw new Exception('Stripe transaction failed');
                }
                break;
        }
    }
}