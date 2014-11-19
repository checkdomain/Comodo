<?php
namespace Checkdomain\Comodo\Model\Result;

/**
 * Class UpdateUserEvClickThroughResult
 *
 * @package Checkdomain\Comodo\Model\Result
 */
class UpdateUserEvClickThroughResult extends AbstractResult
{
    const STATUS_0 = 'Action is not relevant for current order state, was not completed';
    const STATUS_1 = 'Successfully completed';
    const STATUS_2 = 'Email Address was the same and wasn\'t changed. Email was re-sent';

    /**
     * @var integer
     */
    protected $status;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return UpdateUserEvCLickThroughResult
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
