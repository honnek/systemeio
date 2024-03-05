<?php

namespace App\Entity;

use App\Entity\Enum\PaymentProcessor;
use App\Entity\ValueObject\TaxNumber;
use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(Product::class)]
    private ?Product $product = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 50)]
    private ?string $taxNumber = null;

    #[ORM\ManyToOne]
    private ?Coupon $coupon = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [PaymentProcessor::class, 'getValues'])]
    private ?string $paymentProcessor = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $sum = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getTaxNumber(): ?TaxNumber
    {
        if ($this->taxNumber === null) {
            return null;
        }

        return new TaxNumber($this->taxNumber);
    }

    public function setTaxNumber(TaxNumber $taxNumber): static
    {
        $this->taxNumber = $taxNumber->getValue();

        return $this;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getPaymentProcessor(): ?string
    {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor(PaymentProcessor $paymentProcessor): static
    {
        $this->paymentProcessor = $paymentProcessor->value;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(?float $sum): static
    {
        $this->sum = $sum;

        return $this;
    }
}
