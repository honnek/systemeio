<?php

namespace App\Tests\Entity\ValueObject;

use App\Entity\ValueObject\TaxNumber;
use PHPUnit\Framework\TestCase;
use TypeError;

class TaxNumberTest extends TestCase
{
    private function getValidTaxNumberValues(): array
    {
        return [
            'value' => 'DE111234567891',
            'countryCode' => 'DE',
            'country' => 'GERMANY'
        ];
    }

    const INVALID_TAX_NUMBER = 'D1E1111';

    public function testConstructValidTaxNumber()
    {
        $taxNumber = new TaxNumber($this->getValidTaxNumberValues()['value']);
        $this->assertEquals($this->getValidTaxNumberValues()['value'], $taxNumber->getValue());
        $this->assertEquals($this->getValidTaxNumberValues()['countryCode'], $taxNumber->getCountryCode());
    }

    public function testConstructInvalidTaxNumber()
    {
        $taxNumber = new TaxNumber(self::INVALID_TAX_NUMBER);
        $this->assertDoesNotMatchRegularExpression('/^[A-Z]{2}[A-Z0-9]{2}[0-9$]{7,46}/', $taxNumber->getValue());
    }

    public function testConstructEmptyTaxNumber()
    {
        $this->expectException(TypeError::class);
        $taxNumber = new TaxNumber(null);
    }

    public function testGetValidCountry()
    {
        $taxNumber = new TaxNumber($this->getValidTaxNumberValues()['value']);
        $this->assertEquals($this->getValidTaxNumberValues()['country'], $taxNumber->getCountry());
    }

    public function testGetInValidCountry()
    {
        $taxNumber = new TaxNumber(self::INVALID_TAX_NUMBER);
        $this->assertNull($taxNumber->getCountry());
    }
}
