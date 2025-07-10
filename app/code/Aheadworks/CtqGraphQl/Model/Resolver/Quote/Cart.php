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

namespace Aheadworks\CtqGraphQl\Model\Resolver\Quote;

use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Cart extends AbstractResolver
{
    /**
     * Perform resolve method after validate customer authorization
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws AggregateExceptionInterface
     * @throws ClientAware
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $quote = $value['model'];

        $cart = [
            'id' => $quote->getCart()->getQuote()['entity_id'] ?? null,
            'items' => $quote->getCart()->getItems(),
            'shipping_amount' => $quote->getCart()->getQuote()['shipping_amount'] ?? null,
            'subtotal_amount' => $quote->getCart()->getQuote()['subtotal'] ?? null,
            'tax_amount' => $quote->getCart()->getShippingAddress()['tax_amount'] ?? null,
            'order_total_amount' => $quote->getCart()->getQuote()['grand_total'] ?? null
        ];

        return $cart;
    }
}
