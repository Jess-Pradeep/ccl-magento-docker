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

namespace Aheadworks\CtqGraphQl\Plugin;

use Aheadworks\CtqGraphQl\Model\Cart\GetCartForUser as CtqGetCartForUser;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class ExecutePlugin
{
    /**
     * @param CtqGetCartForUser $cartForUser
     */
    public function __construct(
        private readonly CtqGetCartForUser $cartForUser
    ) {
    }

    /**
     * Around execute
     *
     * @param GetCartForUser $subject
     * @param callable $proceed
     * @param string $cartHash
     * @param int|null $customerId
     * @param int $storeId
     * @return Quote
     * @throws GraphQlAuthorizationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     */
    public function aroundExecute(
        GetCartForUser $subject,
        callable $proceed,
        string $cartHash,
        ?int $customerId,
        int $storeId
    ): Quote {
        try {
            $cart = $proceed($cartHash, $customerId, $storeId);
        } catch (GraphQlAuthorizationException) {
            $cart = $this->cartForUser->execute($cartHash, $customerId, $storeId);
        }

        return $cart;
    }
}
