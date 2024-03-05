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
#[Route('/purchase', name: 'create_purchase', methods: ['POST'])]
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
        // Предполагаем что внутри запроса product taxNumber и paymentProcessor всегда определены
        $productId = $request->get('product');
        $taxNumber = $request->get('taxNumber');
        $couponCode = $request->get('couponCode');
        $paymentProcessor = PaymentProcessor::tryFrom($request->get('paymentProcessor'));

        $responseForTotalPrice = $this->forward('App\Controller\CalculateTotalPriceController', [
            'id' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode
        ]);

        // Проверим ответ от CalculateTotalPriceController
        if ($responseForTotalPrice->getStatusCode() !== Response::HTTP_OK) {
            return $this->json(['message' => $responseForTotalPrice->getContent()], Response::HTTP_NOT_FOUND);
        }
        // Проверим существует ли данный PaymentProcessor
        if (!$paymentProcessor) {
            return $this->json(
                ['message' => 'Не найден PaymentProcessor - ' . $request->get('paymentProcessor')],
                Response::HTTP_NOT_FOUND
            );
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

        // Проверяем валидацию $taxNumber и $purchase
        if (!$this->validate($taxNumber)) {
            return $this->json(['message' => (string)$this->validate($taxNumber)], Response::HTTP_NOT_FOUND);
        }
        if (!$this->validate($purchase)) {
            return $this->json(['message' => (string)$this->validate($purchase)], Response::HTTP_NOT_FOUND);
        }

        $this->purchaseManager->save($purchase);

        return $this->json(
            ['message' => 'Покупка продукта с id = ' . $product->getId() . ' успешно завершена!'],
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
