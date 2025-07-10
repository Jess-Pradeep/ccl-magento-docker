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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\CustomerData\QuoteList;

use Magento\Tax\CustomerData\CheckoutTotalsJsLayoutDataProvider;

/**
 * Class TotalsJsLayoutDataProvider
 * @package Aheadworks\Ctq\CustomerData\QuoteList
 */
class TotalsJsLayoutDataProvider extends CheckoutTotalsJsLayoutDataProvider
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'components' => [
                'mini_quotelist_content' => [
                    'children' => [
                        'subtotal.container' => [
                            'children' => [
                                'subtotal' => [
                                    'config' => $this->getTotalsConfig()
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
