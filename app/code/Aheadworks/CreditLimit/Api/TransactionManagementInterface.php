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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Api;

use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface TransactionManagementInterface
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create transaction
     *
     * List of params:
     * customer_id - required
     * action - required
     * amount - depends on action
     * amount_currency - depends on action
     * used_currency - depends on action
     * credit_limit - depends on action
     * other params are optional
     *
     * @param TransactionParametersInterface $params
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws LocalizedException
     */
    public function createTransaction(
        TransactionParametersInterface $params
    ): \Aheadworks\CreditLimit\Api\Data\TransactionInterface;

    /**
     * Create multiple transactions
     *
     * @param TransactionParametersInterface[] $paramsList
     * @return void
     * @throws LocalizedException
     */
    public function createMultipleTransaction(array $paramsList): void;
}
