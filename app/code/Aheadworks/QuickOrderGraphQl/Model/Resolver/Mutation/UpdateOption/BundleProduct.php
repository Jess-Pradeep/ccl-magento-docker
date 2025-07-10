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
 * @package    QuickOrderGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption;

/**
 * Class BundleProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class BundleProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['bundle_options']) && is_array($optionsData['bundle_options'])) {
            $buyRequest['bundle_option'] = [];
            $buyRequest['bundle_option_qty'] = [];
            foreach ($optionsData['bundle_options'] as $bundleOption) {
                $buyRequest['bundle_option'][$bundleOption['id']] = $bundleOption['value'];
                $buyRequest['bundle_option_qty'][$bundleOption['id']] = $bundleOption['quantity'];
            }
        }

        return $buyRequest;
    }
}
