<?php

namespace QuestionKeyBundle;
use Symfony\Component\HttpFoundation\Request;

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
    public function __construct(\DateTime $from = null, \DateTime $to = null)
    {
        $this->from = $from ? $from : new \DateTime("2010-01-01  00:00:00");
        $this->to = $to ? $to : new \DateTime("2030-01-01  00:00:00");
    }

    public function setFromRequest(Request $request)
    {
        if ($request->get('from')) {
            $this->from = new \DateTime($request->get('from'), new \DateTimeZone('Europe/London'));
        }
        if ($request->get('to')) {
            $this->to = new \DateTime($request->get('to'), new \DateTimeZone('Europe/London'));
        }
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
