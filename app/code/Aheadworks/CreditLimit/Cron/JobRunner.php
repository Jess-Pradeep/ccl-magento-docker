<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Cron;

use Aheadworks\CreditLimit\Api\CreditLimitJobManagementInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\CreditLimit\Model\Flag;

/**
 * Class JobRunner
 *
 * @package Aheadworks\CreditLimit\Cron
 */
class JobRunner
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var CreditLimitJobManagementInterface
     */
    private $jobManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param CreditLimitJobManagementInterface $jobManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        CreditLimitJobManagementInterface $jobManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->jobManagement = $jobManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_CL_JOB_RUNNER_LAST_EXEC_TIME)) {
            try {
                $this->jobManagement->runAllJobs();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_CL_JOB_RUNNER_LAST_EXEC_TIME);
        }
    }
}
