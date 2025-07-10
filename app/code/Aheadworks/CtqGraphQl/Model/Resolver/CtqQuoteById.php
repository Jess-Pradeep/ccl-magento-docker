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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\Resolver;

use Aheadworks\Ctq\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\CtqGraphQl\Model\ObjectConverter;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CtqQuoteById extends AbstractResolver
{
    /**
     * @param QuoteRepositoryInterface $repository
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly ObjectConverter $objectConverter
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
     * @return mixed
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        try {
            $this->validateArgs($args);
        } catch (GraphQlInputException $e) {
            return $e;
        }
        $quote = $this->repository->get($args['id']);
        $quoteCustomerId = $quote->getCustomerId() ? $quote->getCustomerId()
            : $quote->getData(CartInterface::AW_CTQ_QUOTE_LIST_CUSTOMER_ID);
        if ($quoteCustomerId !== $context->getUserId()) {
            throw new GraphQlInputException(__('This quote is not of customer. Use unique quote hash.'));
        }

        return $this->objectConverter->convertToArray($quote, QuoteInterface::class);
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
        if (isset($args['id']) && $args['id'] < 1) {
            throw new GraphQlInputException(__('Specify the "entity_id" value.'));
        }

        return true;
    }
}
