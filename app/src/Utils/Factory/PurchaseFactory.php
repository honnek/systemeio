<?php

namespace App\Utils\Factory;

use App\Entity\Coupon;
use App\Entity\Enum\PaymentProcessor;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\ValueObject\TaxNumber;

/**
 * Факторка для создания экземпляра покупки
 */
class PurchaseFactory
{
    public static function make(
        Product $product,
        TaxNumber $taxNumber,
        ?Coupon $coupon,
        PaymentProcessor $paymentProcessor,
        float $sum
    ): Purchase
    {
        $purchase = new Purchase();
        $purchase->setProduct($product);
        $purchase->setTaxNumber($taxNumber);
        $purchase->setCoupon($coupon);
        $purchase->setPaymentProcessor($paymentProcessor);
        $purchase->setSum($sum);

        return $purchase;
    }
}
