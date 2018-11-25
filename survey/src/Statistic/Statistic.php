<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 04/11/2018
 * Time: 12:04
 */

namespace App\Statistic;

class Statistic
{


    private $data = [];
    private $average = 0.0;
    private $variant = 0.0;
    private $numberSample = 0;

    /**
     * Statistic constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * @return float
     */
    public function getAverage()
    {
        return $this->average;
    }

    /**
     * @return float
     */
    public function getVariant()
    {
        return $this->variant;
    }


    /**
     *
     */
    public function calculate()
    {


        $retArray = self::calculateAverage($this->data);
        $this->average = $retArray[0];
        $this->numberSample = $retArray[1];
        $this->variant = self::calculateVariant($this->data, $this->average, $this->numberSample);


    }

    /**
     * @param $data
     * @param $aver
     * @param $n
     * @return float|int
     */
    public static function calculateVariant($data, $aver, $n)
    {
        $s = 0.0;
        foreach ($data as $key=>$value) {
            $s += pow($value[0] - $aver, 2);
        }
        return sqrt($s / ($n - 1));
    }

    /**
     * @param $data
     * @return array
     */
    public static function calculateAverage($data)
    {

        $sum = 0.0;
        $n = 0;
        foreach ($data as $key=>$value) {
            $sum += $value[0] * $value[1];

            $n += $value[1];

        }

        return [$sum / $n, $n];
    }


}