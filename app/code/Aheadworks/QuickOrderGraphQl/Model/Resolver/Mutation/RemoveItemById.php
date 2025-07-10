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

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;

/**
 * Class RemoveItemById
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation
 */
class RemoveItemById implements ResolverInterface
{
    /**
     * @var ProductListItemRepositoryInterface
     */
    private $productListItemRepository;

    /**
     * @param ProductListItemRepositoryInterface $productListItemRepository
     */
    public function __construct(
        ProductListItemRepositoryInterface $productListItemRepository
    ) {
        $this->productListItemRepository = $productListItemRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!isset($args['itemId'])) {
            throw new GraphQlInputException(__('Specify the "itemId" value.'));
        }

        return $this->productListItemRepository->deleteById($args['itemId']);
    }
}
