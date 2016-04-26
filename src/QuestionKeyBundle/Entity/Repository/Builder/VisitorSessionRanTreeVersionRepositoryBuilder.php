<?php

namespace QuestionKeyBundle\Entity\Repository\Builder;

use QuestionKeyBundle\StatsDateRange;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class VisitorSessionRanTreeVersionRepositoryBuilder
{

    /** @var  StatsDateRange */
    protected $dateRange;

    /**
     * @return StatsDateRange
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }

    /**
     * @param StatsDateRange $dateRange
     */
    public function setDateRange($dateRange)
    {
        $this->dateRange = $dateRange;
    }

}

