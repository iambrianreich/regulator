<?php
/**
 * Created by PhpStorm.
 * User: breic
 * Date: 11/28/2018
 * Time: 8:34 AM
 */

namespace RWC\Regulator;

/**
 * Regulator provides a foundation for rate regulation of processes. You specify
 * a number of units to process and a period length (in seconds). As you process
 * work you add quantities to the regulator. You can use isOver() and isUnder()
 * to determine if the Regulator is currently over or under it's regulated rate,
 * and you can use getWaitTime() to determine the number of seconds that a
 * process should wait before processing more work.
 *
 * @package RWC\Regulator
 */
class Regulator
{
    /**
     * The number of items to process in a period.
     *
     * @var float
     */
    protected $amount;

    /**
     * A running count of how many items have processed.
     *
     * @var float
     */
    protected $count;

    /**
     * The length of a period in microtime.
     *
     * @var float
     */
    protected $period;

    /**
     * The starting timestamp in microtime.
     *
     * @var float
     */
    protected $start;

    /**
     * Regulator constructor.
     *
     * @param float $amount The quantity to process in a period.
     * @param float $period The length of a period, in microtime.
     * @param float|null $start The star time time, in microtime.
     * @param float $count The number of items that have processed. Defaults to zero.
     */
    public function __construct(float $amount, float $period, ?float $start = null, ?float $count = 0)
    {
        $this->setAmount($amount);
        $this->setPeriod($period);
        $this->setStart($start);
        $this->setCount($count);
    }

    /**
     * Returns the quantity to process during the period.
     *
     * @return float Returns the quantity to process during the period.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * The quantity to process within the period.
     *
     * @param float $amount The quantity to process within the period.
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Returns the period length in microtime.
     *
     * @return float Returns the period length in microtime.
     */
    public function getPeriod(): float
    {
        return $this->period;
    }

    /**
     * Sets the period in microtime.
     *
     * @param float $period The period in microtime.
     */
    public function setPeriod(float $period): void
    {
        $this->period = $period;
    }

    /**
     * Returns the starting time in microtime.
     *
     * @return float Returns the starting time in microtime.
     */
    public function getStart(): float
    {
        return $this->start;
    }

    /**
     * Sets the starting time, in microtime. If no time is specified the
     * current time is used.
     *
     * @param float|null $start The starting time in microseconds.
     */
    public function setStart(float $start = null): void
    {
        // Default to "now"
        if (is_null($start)) {
            $start = microtime(true);
        }

        $this->start = $start;
    }

    /**
     * Returns the count of how many items have processed.
     *
     * @return float Returns the count of how many items have processed.
     */
    public function getCount() : float
    {
        return $this->count;
    }

    /**
     * Sets a count of how many items have processed.
     *
     * @param float $count A count of how many items have processed.
     */
    public function setCount(?float $count = null) : void
    {
        $count = $count ?? 0;
        $this->count = $count;
    }

    /**
     * Increments the quantity by 1.
     */
    public function incrementQuantity() : void
    {
        $this->addQuantity(1);
    }

    /**
     * Adds an amount to the current quantity processed.
     *
     * @param float $amount The amount to add to the current quantity.
     */
    public function addQuantity(float $amount) : void
    {
        $this->count = $amount;
    }

    /**
     * Returns true if the actual rate of the Regulator (amount over runtime)
     * is greater than the regulated rate (number of items to process in a
     * period).
     *
     * @param float|null $now If set, overrides the current time.
     * @return bool Returns true if the actual rate is too high.
     */
    public function isOver(float $now = null) : bool
    {
        return $this->getActualRate($now) > $this->getRegulatedRate();
    }

    /**
     * Returns true if the actual rate of the Regulator (amount over runtime)
     * is less than or equal to the regulated rate (number of items to process
     * in a period).
     *
     * @param float|null $now If set, overrides the current time.
     * @return bool Returns true if the actual rate is too high.
     */
    public function isUnder(float $now = null) : bool
    {
        return ! $this->isOver($now);
    }

    /**
     * Returns the amount of time, in fractions of seconds, that needs to be
     * waited to process more the specified quantity and stay within the
     * regulated rate.
     *
     * @param float $amount The amount to test.
     * @param float|null $now If set, overrides the current time.
     * @return float Returns the fractional seconds of wait time.
     */
    public function getWaitTime(float $amount, float $now = null) : float
    {
        // Wait time is the current count plus the additional amount
        // multiplied by the regulated rate, minus the current run times
        return (($this->getCount() + $amount) * $this->getRegulatedRate()) -
            $this->getRuntime($now);
    }

    /**
     * Returns the actual rate of the Regulator. This is the ratio of the
     * amount of items processed over the amount of time the regulator has been
     * running.
     *
     * @param float|null $now If set, overrides the current time.
     * @return float Returns the actual rate of the Regulator.
     */
    public function getActualRate(float $now = null) : float
    {
        return $this->getAmount() / $this->getRuntime($now);
    }

    /**
     * Returns the runtime in fractions of seconds.
     *
     * @param float|null $currentTime The current time. Leave null to use current time.
     * @return float Returns the runtime in fractions of seconds.
     */
    public function getRuntime(float $currentTime = null) : float
    {
        $currentTime = $currentTime ?? microtime(true);

        return $currentTime - $this->getStart();
    }

    /**
     * Returns a ratio of the number of items to process over the length of
     * a period, which tells you the number of items that should be allowed to
     * process per second.
     *
     * @return float Returns the number of items to process per-second.
     */
    public function getRegulatedRate() : float
    {
        return $this->getAmount() / $this->getPeriod();
    }
}