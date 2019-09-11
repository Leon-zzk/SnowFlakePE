<?php


if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("UTC");
}

class IDGenerator
{
    const twepoch = 1568084685479;

    const workerIdBits = 10;

    const sequenceBits = 12;

    protected $workId = 0;

    static $lastTimestamp = -1;
    static $sequence = 0;


    function __construct($workId)
    {
        $maxWorkerId = -1 ^ (-1 << self::workerIdBits);
        if ($workId > $maxWorkerId || $workId < 0) {
            throw new Exception("workerId can't be greater than " . $maxWorkerId . " or less than 0");
        }
        $this->workId = $workId;
    }

    public function genId()
    {
        $timestamp = $this->timeGen();
        $lastTimestamp = self::$lastTimestamp;
        if ($timestamp < $lastTimestamp) {
            throw new Exception("Clock moved backwards.  Refusing to generate id for %d milliseconds", ($lastTimestamp - $timestamp));
        }
        if ($lastTimestamp == $timestamp) {
            //同一毫秒内4096个id
            $sequenceMask = -1 ^ (-1 << self::sequenceBits);
//            self::$sequence = (self::$sequence + 1) & $sequenceMask;
//            if (self::$sequence == 0) {
//                //这里多进程（线程）会有问题，建议加锁
//                $timestamp = $this->tilNextMillis($lastTimestamp);
//            }
            self::$sequence++;
            if (self::$sequence >= $sequenceMask) {
                $timestamp = $this->tilNextMillis($lastTimestamp);
            }
        } else {
            self::$sequence = 0;
        }
        self::$lastTimestamp = $timestamp;
        //
        $timestampLeftShift = self::sequenceBits + self::workerIdBits;
        $workerIdShift = self::sequenceBits;
        $nextId = (($timestamp - self::twepoch) << $timestampLeftShift) | ($this->workId << $workerIdShift) | self::$sequence;
        return $nextId;
    }

    protected function timeGen()
    {
        $timestamp = (float)sprintf("%.0f", microtime(true) * 1000);
        return $timestamp;
    }

    protected function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }
        return $timestamp;
    }

}


function hrtime(bool $as_num = false)
{
    $t = microtime(true);

    if ($as_num) {
        return $t * 1000000000;
    }

    $s = floor($t);
    return array(0 => $s, 1 => ($t - $s) * 1000000000);
}

function gethrtime()
{
    $hrtime = hrtime();
    return (($hrtime[0] * 1000000000 + $hrtime[1]) / 1000000000);
}

function start_test()
{
    ob_start();
    return gethrtime();
}

function end_test($start, $name)
{
    global $total;
    $end = gethrtime();
    ob_end_clean();
    $total += $end - $start;
    $num = number_format($end - $start, 3);
    $pad = str_repeat(" ", 24 - strlen($name) - strlen($num));

    echo $name . $pad . $num . "\n";
    ob_start();
    return gethrtime();
}


function total()
{
    global $total;
    $pad = str_repeat("-", 24);
    echo $pad . "\n";
    $num = number_format($total, 3);
    $pad = str_repeat(" ", 24 - strlen("Total") - strlen($num));
    echo "Total" . $pad . $num . "\n";
}

function simple()
{
    $a = 0;
    for ($i = 0; $i < 1000000; $i++)
        $a++;

    $thisisanotherlongname = 0;
    for ($thisisalongname = 0; $thisisalongname < 1000000; $thisisalongname++)
        $thisisanotherlongname++;
}

function userSnowFlake()
{
    $work = new IDGenerator(23);
    for ($i = 0; $i < 10000000; $i++) {
        $id = $work->genId();
//        echo $id . "<br>";
    }
}

function extSnowFlake()
{
    $work = new \Cx\SnowFlake(23);
    for ($i = 0; $i < 10000000; $i++) {
        $id = $work->genId();
//        echo $id . "<br>";
    }
}

$t0 = $t = start_test();
simple();
$t = end_test($t, "simple");
userSnowFlake();
$t = end_test($t, "userSnowFlake");
extSnowFlake();
$t = end_test($t, "userSnowFlake");
total();
