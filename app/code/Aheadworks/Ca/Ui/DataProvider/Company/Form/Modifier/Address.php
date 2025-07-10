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

namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\State;

/**
 * Class Address
 */
class Address implements ModifierInterface
{
    const ADMIN_AREA = Area::AREA_ADMINHTML;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var array
     */
    private $countryOptions;

    /**
     * @var array
     */
    private $regionOptions;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * @param ArrayManager $arrayManager
     * @param CountryCollectionFactory $countryCollection
     * @param RegionCollectionFactory $regionCollection
     * @param DirectoryHelper $directoryHelper
     * @param StoreManagerInterface|null $storeManager
     * @param State $state
     */
    public function __construct(
        ArrayManager $arrayManager,
        CountryCollectionFactory $countryCollection,
        RegionCollectionFactory $regionCollection,
        DirectoryHelper $directoryHelper,
        State $state,
        ?StoreManagerInterface $storeManager = null
    ) {
        $this->arrayManager = $arrayManager;
        $this->countryCollectionFactory = $countryCollection;
        $this->regionCollectionFactory = $regionCollection;
        $this->directoryHelper = $directoryHelper;
        $this->state = $state;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $dictionariesPath = $this->arrayManager->findPath('awCaCompanyProvider', $meta);
        $dictionaries['dictionaries'] = [
            'country_id' => $this->getCountryOptions(),
            'region_id' => $this->getRegionOptions(),
        ];

        if ($dictionariesPath) {
            $meta = $this->arrayManager->merge($dictionariesPath, $meta, $dictionaries);
        } else {
            $dictionariesPath = 'awCaCompanyProvider/arguments/data/js_config';
            $meta = $this->arrayManager->set($dictionariesPath, $meta, $dictionaries);
        }

        return $meta;
    }

    /**
     * Get country options list.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getCountryOptions()
    {
        $toOptionArray = [];
        if (!isset($this->countryOptions)) {
            if ($this->state->getAreaCode() == self::ADMIN_AREA) {
                $stores = $this->storeManager->getStores();
                foreach ($stores as $store) {
                    $countryOptions = $this->countryCollectionFactory->create()->loadByStore($store->getId())->toOptionArray();
                    foreach ($countryOptions as $countryOption) {
                        $countryOption['website_id'] = $store->getWebsiteId();
                        $toOptionArray[] = $countryOption;
                    }
                }
            } else {
                $this->countryOptions = $toOptionArray;
                $toOptionArray = $this->countryCollectionFactory->create()->loadByStore(
                    $this->storeManager->getStore()->getId()
                )->toOptionArray();
            }
            $toOptionArray = $this->orderCountryOptions($toOptionArray);
        }

        return $toOptionArray;
    }

    /**
     * Get region options list.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getRegionOptions()
    {
        if (!isset($this->regionOptions)) {
            $this->regionOptions = $this->regionCollectionFactory->create()->addAllowedCountriesFilter(
                $this->storeManager->getStore()->getId()
            )->toOptionArray();
        }

        return $this->regionOptions;
    }

    /**
     * Sort country options by top country codes.
     *
     * @param array $countryOptions
     * @return array
     */
    private function orderCountryOptions(array $countryOptions)
    {
        $topCountryCodes = $this->directoryHelper->getTopCountryCodes();
        if (empty($topCountryCodes)) {
            return $countryOptions;
        }

        $headOptions = [];
        $tailOptions = [[
            'value' => 'delimiter',
            'label' => '──────────',
            'disabled' => true,
        ]];
        foreach ($countryOptions as $countryOption) {
            if (empty($countryOption['value']) || in_array($countryOption['value'], $topCountryCodes)) {
                $headOptions[] = $countryOption;
            } else {
                $tailOptions[] = $countryOption;
            }
        }
        return array_merge($headOptions, $tailOptions);
    }
}
