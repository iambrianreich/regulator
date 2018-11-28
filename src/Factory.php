<?php
/**
 * Created by PhpStorm.
 * User: breic
 * Date: 11/28/2018
 * Time: 9:36 AM
 */

namespace RWC\Regulator;

/**
 * A factory for creating Regulators easily.
 *
 * @package RWC\Regulator
 */
class Factory
{
    /**
     * We don't instantiate the factory.
     */
    protected function __construct() {}

    /**
     * Creates a Regulator using time strings. The first parameter is the amount
     * of work to process in a period. The second parameter is a valid strtotime()
     * string. The parameter will be used to generate a time based off of the
     * current time, and the difference between the two will be used to generate
     * a period length. For example "+1 hour" would generate a period of one
     * hour. The third parameter overrides the current time if specified. The
     * final parameter can be used to specify the starting count for the
     * Regulator.
     *
     * @param float $amount The quantity to process in a period.
     * @param string $time A valid strtotime() string.
     * @param float|null $start The star time time, in microtime.
     * @param float $count The number of items that have processed. Defaults to zero.
     * @return Regulator
     */
    public static function createFromString(
        float $amount,
        string $time,
        ?float $start = null,
        ?float $count = null
    ) : Regulator {
        $start = $start ?? microtime(true);

        // Convert string to a timestamp, subtract now to get period.
        $period = (float) strtotime($time, (int) $start) - $start;
        return new Regulator($amount, $period, $start, $count);
    }
}