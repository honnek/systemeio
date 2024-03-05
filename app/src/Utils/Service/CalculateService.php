<?php

namespace App\Utils\Service;

use App\Entity\Coupon;
use App\Entity\Enum\CouponType;
use App\Entity\Enum\TaxPercent;
use App\Entity\Product;
use App\Entity\ValueObject\TaxNumber;

/**
 * Сервис для рассчетов
 */
class CalculateService
{
    /**
     * Считает итоговую стоимость товара
     *
     * @TODO допускаем что все страны определены в коде
     *
     * @param Product $product
     * @param TaxNumber $taxNumber
     * @param ?Coupon $coupon
     *
     * @return float
     */
    public function getTotalPrice(Product $product, TaxNumber $taxNumber, ?Coupon $coupon): float
    {
        // Рассчитаем стоимость продукта + налог
        $totalPrice = $product->getPrice() + ($product->getPrice() * TaxPercent::getByCountry($taxNumber->getCountry())->value / 100);

        if ($coupon) {
            switch ($coupon->getType()) {
                case CouponType::FIXED->value:
                    $totalPrice -= $coupon->getValue();
                    break;
                case CouponType::PERCENT->value:
                    $totalPrice -= $totalPrice * $coupon->getValue() / 100;
                    break;
            }
        }

        if ($totalPrice < 0) {
            throw new \RuntimeException('Неверный подсчет цены продукта с id = ' . $product->getId());
        }

        return $totalPrice;
    }
}