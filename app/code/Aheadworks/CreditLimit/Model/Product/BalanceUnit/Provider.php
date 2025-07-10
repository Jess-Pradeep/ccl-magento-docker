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
namespace Aheadworks\CreditLimit\Model\Product\BalanceUnit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\CreditLimit\Model\Product\BalanceUnitInterface;

/**
 * Class Provider
 *
 * @package Aheadworks\CreditLimit\Model\Product\BalanceUnit
 */
class Provider
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $validator
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Validator $validator
    ) {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
    }

    /**
     * Get balance unit
     *
     * @return ProductInterface|Product
     * @throws LocalizedException
     */
    public function getProduct()
    {
        try {
            $balanceUnit = $this->productRepository->get(BalanceUnitInterface::SKU);
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(__('Balance unit product doesn\'t exist'));
        }

        if (!$this->validator->isValid($balanceUnit)) {
            throw new LocalizedException(__('Balance unit product is not available at the moment'));
        }

        return $balanceUnit;
    }
}
