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
namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface HistoryLogSearchResultsInterface
 * @api
 */
interface HistoryLogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get History Log list
     *
     * @return \Aheadworks\Ca\Api\Data\HistoryLogInterface[]
     */
    public function getItems();

    /**
     * Set History Log list
     *
     * @param \Aheadworks\Ca\Api\Data\HistoryLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
