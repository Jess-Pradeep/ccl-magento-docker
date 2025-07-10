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

use Aheadworks\CtqGraphQl\Model\Cart\GetCartForUser as CtqGetCartForUser;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class AddCommentToCartItem extends AbstractResolver
{
    /**
     * @param CtqGetCartForUser $getCartForUser
     * @param CartRepositoryInterface $cartRepository
     * @param array $fields
     */
    public function __construct(
        private readonly CtqGetCartForUser $getCartForUser,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly array $fields = []
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
        $maskedCartId = $args['input']['cart_hash'];
        $cartItems = $args['input']['cart_items'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        try {
            $this->processCartItems($cart, $cartItems);
            $this->cartRepository->save($cart);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
        $items = $cart->getItems();
        $resultItems = [];
        $i = 0;
        foreach ($items as $item) {
            foreach ($this->fields as $key => $name) {
                $resultItems[$i][$key] = $item[$name] ?? null;
            }
            $i++;
        }

        return $resultItems;
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
        if (empty($args['input']['cart_items'])
            || !is_array($args['input']['cart_items'])
        ) {
            throw new GraphQlInputException(__('Required parameter "cart_items" is missing.'));
        }

        return true;
    }

    /**
     * Add comment to cart item
     *
     * @param CartInterface $cart
     * @param array $items
     * @throws GraphQlInputException
     */
    public function processCartItems(CartInterface $cart, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['cart_item_id'])) {
                throw new GraphQlInputException(__('Required parameter "cart_item_id" for "cart_items" is missing.'));
            }
            $itemId = (int)$item['cart_item_id'];
            $itemComment = $item['comment'];
            $cartItem = $cart->getItemById($itemId);
            if ($cartItem && !empty($itemComment)) {
                $cartItem->setAwCtqItemComment($itemComment);
            }
        }
    }
}
