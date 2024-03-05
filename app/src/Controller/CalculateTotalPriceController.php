<?php

namespace App\Controller;

use App\Entity\ValueObject\TaxNumber;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Utils\Service\CalculateService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Контроллер для рассчета итоговой стоимости товара
 */
#[Route('/calculate-price', name: 'calculate_price', methods: ['POST'], format: 'json')]
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
        $data = json_decode($request->getContent(), true);
        if (!isset($data['product']) || !isset($data['taxNumber'])) {
            return $this->json(['message' => 'Not isset product or taxNumber'], Response::HTTP_NOT_FOUND);
        }
        $productId = $data['product'];
        $taxNumber = $data['taxNumber'];

        $couponEntered = isset($data['couponCode']);
        if ($couponEntered) {
            $couponCode = $data['couponCode'];
            $coupon = $this->couponRepo->findByCode($couponCode);
        }

        if ($couponEntered && $coupon === null) {
            return $this->json(['message' => 'Not found coupon with code = ' . $couponCode], Response::HTTP_NOT_FOUND);
        }
        if (($product = $this->productRepo->find($productId)) === null) {
            return $this->json(['message' => 'Not found product with id = ' . $productId], Response::HTTP_NOT_FOUND);
        }

        $taxNumber = new TaxNumber($taxNumber);
        $errors = $this->validator->validate($taxNumber);
        if (count($errors) > 0) {
            return $this->json(['message' => (string)$errors], Response::HTTP_NOT_FOUND);
        }

        try {
            $totalPrice = $this->calculateService->getTotalPrice(
                $product,
                $taxNumber,
                $couponEntered ? $coupon : null
            );
        } catch (Exception $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json([
            'price' => $totalPrice,
        ]);
    }
}
