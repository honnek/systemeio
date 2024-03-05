<?php

namespace App\Entity\ValueObject;

use App\Entity\Enum\CountryCode;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Value Object `Налоговый номер`
 */
final class TaxNumber
{
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [CountryCode::class, 'getValues'])]
    private string $countryCode;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 50)]
    #[Assert\Regex('/^[A-Z]{2}[A-Z0-9]{2}[0-9$]{7,46}/')]
    private string $value;

    /**
     * @param string $taxNumber
     */
    public function __construct(string $taxNumber)
    {
        $this->value = $taxNumber;
        $this->countryCode = substr($taxNumber, 0, 2);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return null|string
     */
    public function getCountry(): ?string
    {
        return CountryCode::tryFrom($this->getCountryCode())?->name;
    }
}
