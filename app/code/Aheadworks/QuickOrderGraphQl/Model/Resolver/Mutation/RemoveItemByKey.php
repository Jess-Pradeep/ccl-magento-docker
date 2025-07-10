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
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RemoveItemByKey
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class RemoveItemByKey implements ResolverInterface
{
    /**
     * @var ProductListManagementInterface
     */
    private $productListService;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param ProductListManagementInterface $productListService
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ProductListManagementInterface $productListService,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->productListService = $productListService;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!isset($args['itemKey'])) {
            throw new GraphQlInputException(__('Specify the "itemKey" value.'));
        }

        try {
            $operationResult = $this->productListService->removeItem($args['itemKey']);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }
}
