<?php

namespace App\Entity;

use App\Entity\Enum\CouponType;
use App\Repository\CouponRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [CouponType::class, 'getValues'])]
    private ?string $type = null;

    #[ORM\Column(length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type('string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\Length(min: 1, max: 50)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type('string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\Length(min: 1, max: 8)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
