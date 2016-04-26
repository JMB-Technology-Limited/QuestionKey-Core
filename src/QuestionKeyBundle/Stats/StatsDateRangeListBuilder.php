<?php

namespace QuestionKeyBundle\Stats;
use QuestionKeyBundle\StatsDateRange;
use Symfony\Component\HttpFoundation\Request;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class StatsDateRangeListBuilder
{

    /** @var  StatsDateRange */
    protected $dateRange;

    protected $interval;


    public function __construct(StatsDateRange $statsDateRange, $interval)
    {
        $this->dateRange = $statsDateRange;
        $this->interval = $interval;
    }


    public function build() {
        $data = array();
        $currentFrom = clone $this->dateRange->getFrom();
        $interval = new \DateInterval($this->interval);
        $interval1Sec = new \DateInterval("PT1S");
        while($currentFrom < $this->dateRange->getTo()) {
            $currentTo = clone $currentFrom;
            $currentTo->add($interval);
            $currentTo->sub($interval1Sec);
            $data[] = new StatsDateRange(clone $currentFrom, $currentTo);
            $currentFrom->add($interval);
        }
        return $data;
    }

}
