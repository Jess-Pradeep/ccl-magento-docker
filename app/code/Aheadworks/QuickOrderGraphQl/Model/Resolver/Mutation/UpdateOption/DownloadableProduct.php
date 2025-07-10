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
 * Class DownloadableProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class DownloadableProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['links']) && is_array($optionsData['links'])) {
            $buyRequest['links'] = [];
            foreach ($optionsData['links'] as $link) {
                $buyRequest['links'][] = $link['link_id'];
            }
        }

        return $buyRequest;
    }
}
