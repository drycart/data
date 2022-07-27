<?php

/*
 *  @copyright (c) 2018 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

use DivisionByZeroError;
use OutOfBoundsException;
use UnexpectedValueException;

/**
 * Simple Money object
 * store money at integer format for stable math result
 *
 * @author mendel
 */
class Money
{
    /**
     * Amount (integer, at "cents" format)
     * @var int
     */
    protected $amount;

    /**
     * Currency id
     * @var string
     */
    protected $currency;

    /**
     * Exponent - array where key is currency, value - number of digits after dot
     * @var array
     */
    protected static $exponent = ['USD' => 2, 'EUR' => 2, 'ILS' => 2];

    /**
     * Currency used if it not selected
     * @var string
     */
    protected static $defaultCurrency = 'USD';

    /**
     * Constructor
     * if currency is null/empty => use default currency
     *
     * @param int $amount
     * @param string|null $currency
     */
    public function __construct(int $amount, ?string $currency = null)
    {
        $this->amount = $amount;
        $this->currency = strtoupper($currency ?? static::$defaultCurrency);
    }

    /**
     * Get amount at "cents"
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Get currency
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Init default currency and exponents
     *
     * @param array $exponent
     * @param string $defaultCurrency
     * @return void
     */
    public static function init(array $exponent, string $defaultCurrency): void
    {
        static::$exponent = $exponent;
        static::$defaultCurrency = strtoupper($defaultCurrency);
    }

    /**
     * Helper for instantiate money object from float value
     * best way - always use integer, but at data input we can take float,
     * so its sugar for it
     *
     * @param float $amount
     * @param string|null $currency
     * @return Money
     */
    public static function fromFloat(float $amount, ?string $currency = null): Money
    {
        $currency = strtoupper($currency ?? static::$defaultCurrency);
        $amount *= pow(10, self::exponent($currency));
        return new Money($amount, $currency);
    }

    /**
     * Get exponent size for currency
     * @param string $currency
     * @return int
     * @throws OutOfBoundsException
     */
    public static function exponent(string $currency): int
    {
        $currency = strtoupper($currency);
        if (empty(self::$exponent[$currency])) {
            throw new OutOfBoundsException('Unknown currency: ' . $currency);
        }
        return self::$exponent[$currency];
    }

    /**
     * Get string format for amount, compatible to float
     * i.e. can be converted to float or used as is
     * @return string
     */
    public function amountString(): string
    {
        return number_format(
            $this->amount / pow(10, self::exponent($this->currency)),
            self::exponent($this->currency),
            '.',
            ''
        );
    }

    /**
     * Pretty money format, contain amount and currency
     * @2DO: add different formats/templates for each currency
     * @return string
     */
    public function __toString()
    {
        return number_format(
            $this->amount / pow(10, self::exponent($this->currency)),
            self::exponent($this->currency),
            '.',
            ' '
        ) . ' ' . $this->currency;
    }

    /**
     * Get money which our currency, but zero amount
     * @return Money
     */
    public function zero(): Money
    {
        return new Money(0, $this->currency);
    }

    /**
     * True if zero at account
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ($this->amount == 0);
    }

    /**
     * Check if currency of money is same
     * @param Money $x
     * @param Money $y
     * @return void
     * @throws UnexpectedValueException
     */
    public static function checkCurrency(Money $x, Money $y): void
    {
        if ($x->currency != $y->currency) {
            throw new UnexpectedValueException('Currency will be same');
        }
    }

    /**
     * Add other money, and return new (immutable)
     * @param Money $y
     * @return Money
     * @throws UnexpectedValueException
     */
    public function add(Money $y): Money
    {
        static::checkCurrency($this, $y);
        $amount = $this->amount + $y->amount;
        return new Money($amount, $this->currency);
    }

    /**
     * Subtracts other money and return new (immutable)
     * @param Money $y
     * @return Money
     * @throws UnexpectedValueException
     */
    public function subtract(Money $y): Money
    {
        static::checkCurrency($this, $y);
        $amount = $this->amount - $y->amount;
        return new Money($amount, $this->currency);
    }

    /**
     * Multiply to integer
     * @param int $y
     * @return Money
     */
    public function multiply(int $y): Money
    {
        $amount = (int) $this->amount * $y;
        return new Money($amount, $this->currency);
    }

    /**
     * Divide by integer
     * @param int $y
     * @return Money
     * @throws DivisionByZeroError
     */
    public function divide(int $y): Money
    {
        $amount = intdiv($this->amount, $y);
        return new Money($amount, $this->currency);
    }

    /**
     * Returns remainder (modulo) of the division
     * @param int $y
     * @return Money
     * @throws DivisionByZeroError
     */
    public function remainder(int $y): Money
    {
        $amount = $this->amount % $y;
        return new Money($amount, $this->currency);
    }

    /**
     *
     * @param Money $a
     * @param Money $b
     * @return Money
     * @throws UnexpectedValueException
     */
    public static function min(Money $a, Money $b): Money
    {
        static::checkCurrency($a, $b);
        if ($a->amount < $b->amount) {
            return new Money($a->amount, $a->currency);
        } else {
            return new Money($b->amount, $b->currency);
        }
    }

    /**
     *
     * @param Money $a
     * @param Money $b
     * @return Money
     * @throws UnexpectedValueException
     */
    public static function max(Money $a, Money $b): Money
    {
        static::checkCurrency($a, $b);
        if ($a->amount > $b->amount) {
            return new Money($a->amount, $a->currency);
        } else {
            return new Money($b->amount, $b->currency);
        }
    }

    /**
     *
     * @param Money $a
     * @param Money $b
     * @return int
     * @throws UnexpectedValueException
     */
    public static function compare(Money $a, Money $b): int
    {
        static::checkCurrency($a, $b);
        return $a->amount <=> $b->amount;
    }
}
