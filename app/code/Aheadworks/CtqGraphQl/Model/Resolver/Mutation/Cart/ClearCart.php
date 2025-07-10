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

namespace Aheadworks\CtqGraphQl\Model\Resolver\Mutation\Cart;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\CtqGraphQl\Model\Cart\GetCartForUser;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ClearCart extends AbstractResolver
{
    /**
     * @param GetCartForUser $getCartForUser
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        private readonly GetCartForUser $getCartForUser,
        private readonly BuyerQuoteManagementInterface $buyerQuoteManagement
    ) {
    }

    /**
     * Perform resolve method
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return bool
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
        $this->validateArgs($args);

        $maskedCartId = $args['input']['cart_hash'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        return $this->buyerQuoteManagement->clearCart($cart);
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
        if (empty($args['input']['cart_hash'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing.'));
        }

        return true;
    }
}
