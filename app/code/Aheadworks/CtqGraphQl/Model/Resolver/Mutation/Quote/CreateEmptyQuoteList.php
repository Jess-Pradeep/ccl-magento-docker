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

namespace Aheadworks\CtqGraphQl\Model\Resolver\Mutation\Quote;

use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreateEmptyQuoteList extends AbstractResolver
{
    /**
     * @param CreateEmptyCartForCustomer $createEmptyCartForCustomer
     * @param CreateEmptyCartForGuest $createEmptyCartForGuest
     */
    public function __construct(
        private readonly CreateEmptyCartForCustomer $createEmptyCartForCustomer,
        private readonly CreateEmptyCartForGuest $createEmptyCartForGuest
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
     * @return string
     * @throws AggregateExceptionInterface
     * @throws ClientAware
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $customerId = $context->getUserId();
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $customerGroupId = (int)$context->getExtensionAttributes()->getCustomerGroupId();

        $maskedQuoteId = (0 === $customerId || null === $customerId)
            ? $this->createEmptyCartForGuest->execute($customerGroupId, $storeId)
            : $this->createEmptyCartForCustomer->execute($customerId, $storeId);

        return $maskedQuoteId;
    }
}
