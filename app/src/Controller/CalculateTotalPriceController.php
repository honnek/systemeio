<?php

namespace App\Controller;

use App\Entity\ValueObject\TaxNumber;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Utils\Service\CalculateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Контроллер для рассчета итоговой стоимости товара
 */
#[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
class CalculateTotalPriceController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CalculateService   $calculateService,
        private readonly ProductRepository  $productRepo,
        private readonly CouponRepository   $couponRepo,
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(
        Request $request,
    ): JsonResponse
    {
        // Предполагаем что внутри запроса product и taxNumber всегда определены
        $productId = $request->get('product');
        $taxNumber = $request->get('taxNumber');
        $couponCode = $request->get('couponCode');

        $couponEntered = isset($couponCode);
        if ($couponEntered) {
            $coupon = $this->couponRepo->findByCode($couponCode);
        }

        if ($couponEntered && $coupon === null) {
            return $this->json(['message' => 'Не найден купон с code = ' . $couponCode], Response::HTTP_NOT_FOUND);
        }
        if (($product = $this->productRepo->find($productId)) === null) {
            return $this->json(['message' => 'Не найден продукт с id = ' . $productId], Response::HTTP_NOT_FOUND);
        }

        $taxNumber = new TaxNumber($taxNumber);
        $errors = $this->validator->validate($taxNumber);
        if (count($errors) > 0) {
            return $this->json(['message' => (string)$errors], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'price' => $this->calculateService->getTotalPrice(
                $product,
                $taxNumber,
                $couponEntered ? $coupon : null
            ),
        ]);
    }
}
