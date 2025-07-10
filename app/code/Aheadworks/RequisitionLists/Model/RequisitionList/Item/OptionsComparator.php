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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;

class OptionsComparator extends Comparator
{
    /**
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        private readonly OptionConverter $optionConverter
    ) {
        parent::__construct($optionConverter);
    }

    /**
     * Compare two requisition list items
     *
     * @param RequisitionListItemInterface $target
     * @param RequisitionListItemInterface $source
     * @return bool
     */
    public function compareIfEqual($target, $source): bool
    {
        $targetBuyRequest = $this->optionConverter->toBuyRequest(
            $target->getProductType(),
            $target->getProductOption()
        )->getData();
        $sourceBuyRequest = $this->optionConverter->toBuyRequest(
            $source->getProductType(),
            $source->getProductOption()
        )->getData();

        if ($targetBuyRequest != $sourceBuyRequest) {
            return false;
        }

        return true;
    }
}
