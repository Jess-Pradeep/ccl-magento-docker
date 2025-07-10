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
 * @package    QuickOrderGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\GraphQl\Model\Query\ContextInterface;
use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;

/**
 * Class ProductListForCurrentCustomer
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class ProductListForCurrentCustomer implements ResolverInterface
{
    /**
     * @var ProductListRepositoryInterface
     */
    private $productListRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param ProductListRepositoryInterface $productListRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ProductListRepositoryInterface $productListRepository,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->productListRepository = $productListRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $productList = $this->productListRepository->getByCustomerId($context->getUserId());
        return $this->dataObjectProcessor->buildOutputDataArray(
            $productList,
            ProductListInterface::class
        );
    }
}
