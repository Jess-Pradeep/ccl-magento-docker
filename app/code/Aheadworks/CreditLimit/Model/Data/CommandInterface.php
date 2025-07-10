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
namespace Aheadworks\CreditLimit\Model\Data;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface CommandInterface
 *
 * @package Aheadworks\CreditLimit\Model\Data
 */
interface CommandInterface
{
    /**
     * Execute command
     *
     * @param mixed $data
     * @return mixed
     * @throws LocalizedException
     */
    public function execute($data);
}
