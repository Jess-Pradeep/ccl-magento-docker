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
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\IsNotSalableForRequestedQtyMessageProvider;
use Aheadworks\QuickOrder\Model\ThirdPartyModule\BaseFactory;

class AwGiftCardProvider extends AbstractProvider
{
    /**
     * @param IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider
     * @param BaseFactory $optionRendererFactory
     */
    public function __construct(
        IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider,
        private BaseFactory $optionRendererFactory
    ) {
        parent::__construct($isNotSalableMessageProvider);
    }

    /**
     * Is editable
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return true;
    }

    /**
     * Is qty editable
     *
     * @return bool
     */
    public function isQtyEditable(): bool
    {
        return true;
    }

    /**
     * Get product attributes specific for product type
     *
     * @param array $orderOptions
     * @return array
     */
    protected function getProductTypeAttributes($orderOptions)
    {
        $result = [];
        $optionRenderer = $this->optionRendererFactory->create();
        if ($optionRenderer) {
            $result = $optionRenderer->render($orderOptions);
        }

        return $result;
    }
}
