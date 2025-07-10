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

namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Country
 */
class Country implements OptionSourceInterface
{
    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        CountryCollectionFactory $countryCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->countryCollectionFactory = $countryCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare countries
     *
     * @throws NoSuchEntityException
     */
    public function toOptionArray()
    {
        $toOptionArray = [];
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $countryOptions = $this->countryCollectionFactory->create()->loadByStore($store->getId())->toOptionArray();
            foreach ($countryOptions as $countryOption) {
                $toOptionArray[] = $countryOption;
            }
        }

        return array_unique($toOptionArray, 0);
    }
}
