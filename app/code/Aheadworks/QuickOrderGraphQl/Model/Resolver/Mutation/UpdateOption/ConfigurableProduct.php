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
 * Class ConfigurableProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class ConfigurableProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['super_attribute']) && is_array($optionsData['super_attribute'])) {
            $buyRequest['super_attribute'] = [];
            foreach ($optionsData['super_attribute'] as $superAttribute) {
                $buyRequest['super_attribute'][$superAttribute['option_id']] = $superAttribute['option_value'];
            }
        }

        return $buyRequest;
    }
}
