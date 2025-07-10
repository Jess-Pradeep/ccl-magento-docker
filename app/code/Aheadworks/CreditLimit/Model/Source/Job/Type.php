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
namespace Aheadworks\CreditLimit\Model\Source\Job;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Aheadworks\CreditLimit\Model\Source\Job
 */
class Type implements OptionSourceInterface
{
    /**
     * Job type for changing credit limit value
     */
    const UPDATE_CREDIT_LIMIT = 'update_credit_limit';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::UPDATE_CREDIT_LIMIT,
                'label' => __('Update Credit Limit')
            ]
        ];
    }
}
