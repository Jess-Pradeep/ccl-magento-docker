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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Config\Backend;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnitFactory;
use Aheadworks\CreditLimit\Model\Product\BalanceUnitInterface;

/**
 * Class CreditBalanceUpdate
 *
 * @package Aheadworks\CreditLimit\Model\Config\Backend
 */
class CreditBalanceUpdate extends ConfigValue
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var BalanceUnitFactory
     */
    private $balanceUnitFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ProductRepositoryInterface $productRepository
     * @param BalanceUnitFactory $balanceUnitFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ProductRepositoryInterface $productRepository,
        BalanceUnitFactory $balanceUnitFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->productRepository = $productRepository;
        $this->balanceUnitFactory = $balanceUnitFactory;
    }

    /**
     * Create balance unit product if option is enabled
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        if ($this->getValue()) {
            $this->ensureBalanceUnitProduct();
        }
        parent::beforeSave();

        return $this;
    }

    /**
     * Ensure balance unit product exists or creates a new one
     *
     * @throws LocalizedException
     */
    private function ensureBalanceUnitProduct()
    {
        try {
            $this->productRepository->get(BalanceUnitInterface::SKU);
        } catch (NoSuchEntityException $exception) {
            $product = $this->balanceUnitFactory->create();
            $this->productRepository->save($product);
        }
    }
}
