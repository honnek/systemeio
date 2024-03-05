<?php

namespace App\Controller;

use App\Entity\Enum\PaymentProcessor;
use App\Entity\ValueObject\TaxNumber;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Utils\Factory\PurchaseFactory;
use App\Utils\Manager\PurchaseManager;
use App\Utils\Service\PaymentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Контроллер для создания покупки
 */
#[Route('/purchase', name: 'create_purchase', methods: ['POST'], format: 'json')]
class PurchaseController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly PaymentService     $paymentService,
        private readonly PurchaseManager    $purchaseManager,
        private readonly CouponRepository   $couponRepo,
        private readonly ProductRepository  $productRepo,
    )
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(
        Request $request,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['product']) || !isset($data['taxNumber']) || !isset($data['paymentProcessor'])) {
            return $this->json(
                ['message' => 'Not isset product or taxNumber or paymentProcessor'],
                Response::HTTP_NOT_FOUND
            );
        }
        $productId = $data['product'];
        $taxNumber = $data['taxNumber'];
        $couponCode = $data['couponCode'] ?? null;

        $paymentProcessor = PaymentProcessor::tryFrom($data['paymentProcessor']);
        // Проверим существует ли данный PaymentProcessor
        if (!$paymentProcessor) {
            return $this->json(
                ['message' => 'Not found PaymentProcessor - ' . $request->get('paymentProcessor')],
                Response::HTTP_NOT_FOUND
            );
        }

        $responseForTotalPrice = $this->forward('App\Controller\CalculateTotalPriceController', [
            'id' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode
        ]);
        // Проверим ответ от CalculateTotalPriceController
        if ($responseForTotalPrice->getStatusCode() !== Response::HTTP_OK) {
            return $this->json(['message' => $responseForTotalPrice->getContent()], Response::HTTP_NOT_FOUND);
        }

        $totalPrice = json_decode($responseForTotalPrice->getContent())?->price;
        $coupon = $this->couponRepo->findByCode($couponCode);
        $product = $this->productRepo->find($productId);

        // Пробуем провести оплату
        try {
            $this->paymentService->pay($paymentProcessor, $totalPrice);
        } catch (Exception $exception) {
            return $this->json(
                ['message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $taxNumber = new TaxNumber($taxNumber);
        $purchase = PurchaseFactory::make($product, $taxNumber, $coupon, $paymentProcessor, $totalPrice);

        // Проверяем на валидность $taxNumber и $purchase
        if ($this->validate($taxNumber)) {
            return $this->json(['message' => (string)$this->validate($taxNumber)], Response::HTTP_NOT_FOUND);
        }
        if ($this->validate($purchase)) {
            return $this->json(['message' => (string)$this->validate($purchase)], Response::HTTP_NOT_FOUND);
        }

        $this->purchaseManager->save($purchase);

        return $this->json(
            ['message' => 'Product with id = ' . $product->getId() . ' purchase success!'],
            Response::HTTP_CREATED
        );
    }

    private function validate(object $entity): ConstraintViolationListInterface|null
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            return $errors;
        }

        return null;
    }
}
