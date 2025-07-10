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

namespace Aheadworks\CreditLimit\Model\Config\Source\Import;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Types
 */
class Types implements OptionSourceInterface
{
    const CREDIT_UPDATE_CUSTOMERS = 'credit_update_customers';
    const CREDIT_UPDATE_COMPANIES = 'credit_update_companies';

    /**
     * @var array
     */
    private $options;

    /**
     * Get all options
     *
     * @return array[]
     */
    public function getAllOptions(): array
    {
        return [
            [
                'value' => self::CREDIT_UPDATE_CUSTOMERS,
                'label' => __('Credit Update of Customers')
            ],
            [
                'value' => self::CREDIT_UPDATE_COMPANIES,
                'label' => __('Credit Update of Companies')
            ]
        ];
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = $this->getAllOptions();
        }

        return $this->options;
    }
}
