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
namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job
 */
interface ProcessorInterface
{
    /**
     * Execute job
     *
     * @param array $configuration
     * @return bool
     */
    public function process($configuration);
}
