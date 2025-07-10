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
 * Class DefaultProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class DefaultProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($productOption)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getQtySalableMessage($requestedQty)
    {
        return $this->getIsNotSalableMessageForRequestedQty($this->getProduct(), $requestedQty);
    }
}
