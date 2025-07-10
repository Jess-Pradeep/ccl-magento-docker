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
namespace Aheadworks\Ctq\ViewModel\Customer\Export\Quote;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store as StoreModel;

/**
 * Class Store
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Export\Quote
 */
class Store implements ArgumentInterface
{
    /**
     * @var StoreInformation
     */
    private $storeInformation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreInformation $storeInformation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreInformation $storeInformation,
        StoreManagerInterface $storeManager
    ) {
        $this->storeInformation = $storeInformation;
        $this->storeManager = $storeManager;
    }

    /**
     * Get formatted store information
     *
     * @param Quote $quote
     * @return bool
     * @throws NoSuchEntityException
     */
    public function getFormattedStoreInformation($quote)
    {
        /** @var StoreModel $store */
        $store = $this->storeManager->getStore($quote->getStoreId());
        return $this->storeInformation->getFormattedAddress($store);
    }
}
