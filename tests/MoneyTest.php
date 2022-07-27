<?php
/*
 * @copyright (c) 2020 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */
namespace drycart\data\tests;
use drycart\data\Money;
use PHPUnit\Framework\TestCase;

/**
 * @author mendel
 */
class MoneyTest extends TestCase
{
    public function testBasic()
    {
        Money::init(['EUR'=>2], 'EUR');

        $moneyEur = new Money(100);
        $this->assertEquals('1.00 EUR', (string) $moneyEur);
        $this->assertEquals(100, $moneyEur->getAmount());
        $this->assertEquals('EUR', $moneyEur->getCurrency());
        $this->assertEquals('1.00', $moneyEur->amountString());
        $this->assertFalse($moneyEur->isEmpty());
        
        $moneyEur0 = $moneyEur->zero();
        $this->assertEquals('0.00 EUR', (string) $moneyEur0);
        $this->assertEquals(0, $moneyEur0->getAmount());
        $this->assertEquals('EUR', $moneyEur0->getCurrency());
        $this->assertEquals('0.00', $moneyEur0->amountString());
        $this->assertTrue($moneyEur0->isEmpty());

        Money::init(['USD'=>2, 'ILS'=>2], 'USD');

        $moneyUsd = new Money(100500);
        $this->assertEquals('1 005.00 USD', (string) $moneyUsd);
        $this->assertEquals(100500, $moneyUsd->getAmount());
        $this->assertEquals('USD', $moneyUsd->getCurrency());
        $this->assertEquals('1005.00', $moneyUsd->amountString());
        $this->assertFalse($moneyUsd->isEmpty());
        
        $moneyIls = new Money(250, 'ils');
        $this->assertEquals('2.50 ILS', (string) $moneyIls);
        $this->assertEquals(250, $moneyIls->getAmount());
        $this->assertEquals('ILS', $moneyIls->getCurrency());
        $this->assertEquals('2.50', $moneyIls->amountString());
        $this->assertFalse($moneyIls->isEmpty());
        
        $moneyUsd2 = Money::fromFloat(1.23);
        $this->assertEquals('1.23 USD', (string) $moneyUsd2);
        $this->assertEquals(123, $moneyUsd2->getAmount());
        $this->assertEquals('USD', $moneyUsd2->getCurrency());
        $this->assertEquals('1.23', $moneyUsd2->amountString());
        
        $moneyIls2 = Money::fromFloat(2.5, 'ILS');
        $this->assertEquals('2.50 ILS', (string) $moneyIls2);
        $this->assertEquals(250, $moneyIls2->getAmount());
        $this->assertEquals('ILS', $moneyIls2->getCurrency());
        $this->assertEquals('2.50', $moneyIls2->amountString());
        
        $moneyUsd3 = $moneyUsd->add($moneyUsd2);
        $this->assertEquals('1 006.23 USD', (string) $moneyUsd3);
        $this->assertEquals(100623, $moneyUsd3->getAmount());
        $this->assertEquals('USD', $moneyUsd3->getCurrency());
        $this->assertEquals('1006.23', $moneyUsd3->amountString());
        $this->assertNotEquals(spl_object_hash($moneyUsd3),spl_object_hash($moneyUsd));
        $this->assertNotEquals(spl_object_hash($moneyUsd3),spl_object_hash($moneyUsd2));
        
        $moneyUsd4 = $moneyUsd3->subtract($moneyUsd2);
        $this->assertEquals('1 005.00 USD', (string) $moneyUsd4);
        $this->assertEquals(100500, $moneyUsd4->getAmount());
        $this->assertEquals('USD', $moneyUsd4->getCurrency());
        $this->assertEquals('1005.00', $moneyUsd4->amountString());
        $this->assertNotEquals(spl_object_hash($moneyUsd4),spl_object_hash($moneyUsd3));
        $this->assertNotEquals(spl_object_hash($moneyUsd4),spl_object_hash($moneyUsd2));
                
        $moneyIls3 = $moneyIls2->multiply(2);
        $this->assertEquals('5.00 ILS', (string) $moneyIls3);
        $this->assertEquals(500, $moneyIls3->getAmount());
        $this->assertEquals('ILS', $moneyIls3->getCurrency());
        $this->assertNotEquals(spl_object_hash($moneyIls2),spl_object_hash($moneyIls3));
                
        $moneyIls4 = $moneyIls3->divide(7);
        $this->assertEquals('0.71 ILS', (string) $moneyIls4);
        $this->assertEquals(71, $moneyIls4->getAmount());
        $this->assertEquals('ILS', $moneyIls4->getCurrency());
        $this->assertNotEquals(spl_object_hash($moneyIls3),spl_object_hash($moneyIls4));
                
        $moneyIls5 = $moneyIls3->remainder(7);
        $this->assertEquals('0.03 ILS', (string) $moneyIls5);
        $this->assertEquals(3, $moneyIls5->getAmount());
        $this->assertEquals('ILS', $moneyIls5->getCurrency());
        $this->assertNotEquals(spl_object_hash($moneyIls3),spl_object_hash($moneyIls5));
        
        $moneyIlsMin = Money::min($moneyIls4, $moneyIls5);
        $this->assertEquals('0.03 ILS', (string) $moneyIlsMin);
        $this->assertEquals(3, $moneyIlsMin->getAmount());
        $this->assertEquals('ILS', $moneyIlsMin->getCurrency());
        $this->assertNotEquals(spl_object_hash($moneyIls5),spl_object_hash($moneyIlsMin));
        
        $moneyIlsMax = Money::max($moneyIls4, $moneyIls5);
        $this->assertEquals('0.71 ILS', (string) $moneyIlsMax);
        $this->assertEquals(71, $moneyIlsMax->getAmount());
        $this->assertEquals('ILS', $moneyIlsMax->getCurrency());
        $this->assertNotEquals(spl_object_hash($moneyIls4),spl_object_hash($moneyIlsMax));



    }
}
