<?php
/**
 * Created by PhpStorm.
 * User: breic
 * Date: 11/28/2018
 * Time: 10:01 AM
 */

require_once('vendor/autoload.php');

$regulator = \RWC\Regulator\Factory::createFromString(1000, '+1 hour');

for($i = 0; $i < 5; $i++) {
    echo "Runtime: " . $regulator->getRuntime() . "\r\n";
    sleep(1);
}

echo "Regulated rate is " . $regulator->getRegulatedRate() . " per second \r\n";
echo "Regulated rate is " . ($regulator->getRegulatedRate() * 60 * 60) . " per hour\r\n";

$regulator->addQuantity(50);
echo "Regulator has run " . $regulator->getCount() . " items\r\n";

if ($regulator->isOver()) {
    echo "Regulator is over. Regulated Rate: " . $regulator->getRegulatedRate() . ", Actual rate: " . $regulator->getActualRate() . "\r\n";
    echo "Waiting " . $regulator->getWaitTime(1) . " seconds to send 1 item\r\n";
    sleep($regulator->getWaitTime(1));
} else {
    echo "Regulator is under. Actual rate: " . $regulator->getActualRate() . "\r\n";
}


$regulator->addQuantity(1);
echo "Regulator has run " . $regulator->getCount() . " items\r\n";

if ($regulator->isOver()) {
    echo "Regulator is over. Actual rate: " . $regulator->getActualRate() . "\r\n";
    echo "Waiting " . $regulator->getWaitTime(1) . " seconds to send 1 item\r\n";
    sleep($regulator->getWaitTime(1));
} else {
    echo "Regulator is under. Actual rate: " . $regulator->getActualRate() . "\r\n";
}