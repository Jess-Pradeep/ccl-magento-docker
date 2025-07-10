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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Export\Item;

use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface to provide items collection for export
 */
interface CollectionFactoryInterface
{
    /**
     * Create filtered collection
     *
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return AbstractCollection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): AbstractCollection;
}
