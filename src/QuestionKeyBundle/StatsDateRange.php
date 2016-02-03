<?php

namespace QuestionKeyBundle;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class StatsDateRange
{

    /** @var  \DateTime */
    protected $from;

    /** @var  \DateTime */
    protected $to;

    /**
     * StatsDateRange constructor.
     */
    public function __construct()
    {
        $this->from = new \DateTime("2010-01-01  00:00:00");
        $this->to = new \DateTime("2030-01-01  00:00:00");
    }

    /**
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }
    
}
