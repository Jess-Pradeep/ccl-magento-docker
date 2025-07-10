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
namespace Aheadworks\CreditLimit\Model\Customer\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider as CreditLimitDataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TotalList
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Layout\Processor
 */
class TotalList
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CreditLimitDataProvider
     */
    private $creditLimitDataProvider;

    /**
     * @param ArrayManager $arrayManager
     * @param CreditLimitDataProvider $creditLimitDataProvider
     */
    public function __construct(
        ArrayManager $arrayManager,
        CreditLimitDataProvider $creditLimitDataProvider
    ) {
        $this->arrayManager = $arrayManager;
        $this->creditLimitDataProvider = $creditLimitDataProvider;
    }

    /**
     * Process js layout of block
     *
     * @param array $jsLayout
     * @param int $customerId
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function process($jsLayout, $customerId, $websiteId)
    {
        $optionsProviderPath = 'components/aw_cl_top_totals_data_provider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => $this->creditLimitDataProvider->getData($customerId, $websiteId)
            ]
        );

        return $jsLayout;
    }
}
