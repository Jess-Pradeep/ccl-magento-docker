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
 * @package    RequisitionListsGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionListsGraphQl\Model\Resolver\Mutation\List\Item;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager;
use Aheadworks\RequisitionListsGraphQl\Model\Data\DataProcessorInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\AbstractPrivateResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class AddItem extends AbstractPrivateResolver
{
    /**
     * @param DataProcessorInterface $prepareDataProcessor
     * @param Manager $manager
     */
    public function __construct(
        private readonly DataProcessorInterface $prepareDataProcessor,
        private readonly Manager $manager
    ) {
    }

    /**
     * Perform resolve method after validate customer authorization
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return RequisitionListItemInterface[]
     * @throws AggregateExceptionInterface
     * @throws ClientAware|LocalizedException
     */
    protected function performResolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $data['item'] = $this->prepareDataProcessor->prepareData($args['item']);
        $result = $this->manager->importItemsFromData([$data['item']], (int)$args['list_id']);

        return gettype($result) === 'array' ? $result : [$result];
    }

    /**
     * Validate requested arguments
     *
     * @param array $args
     * @return bool|GraphQlInputException
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): bool|GraphQlInputException
    {
        if (empty($args['list_id'])) {
            throw new GraphQlInputException(__('Required parameter "list_id" is missing.'));
        }

        if (empty($args['item'])) {
            throw new GraphQlInputException(__('Required parameter "item" is missing.'));
        }

        if (empty($args['item']['sku'])) {
            throw new GraphQlInputException(__('Required parameter "item/sku" is missing.'));
        }

        return true;
    }
}
