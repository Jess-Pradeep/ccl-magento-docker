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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

/**
 * Class BundleProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class BundleProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['bundle_options']) ? array_values($orderOptions['bundle_options']) : [];
    }

    /**
     * @inheritdoc
     */
    public function getQtySalableMessage($requestedQty)
    {
        $message = '';
        foreach ($this->subProducts as $product) {
            $qty = $product->getCartQty() * $requestedQty;
            $resultMessage = $this->getIsNotSalableMessageForRequestedQty($product, $qty);
            if ($resultMessage) {
                $message = $resultMessage;
                break;
            }
        }

        return $message;
    }
}
