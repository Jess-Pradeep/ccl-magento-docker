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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Data\Command\Balance;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\CreditLimit\Model\Data\CommandInterface;
use Aheadworks\CreditLimit\Api\CartManagementInterface;

/**
 * Class MakePayment
 *
 * @package Aheadworks\CreditLimit\Controller\Balance
 */
class MakePayment implements CommandInterface
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute($data)
    {
        if (!isset($data['quote'])) {
            throw new \InvalidArgumentException('Argument quote is required');
        }
        if (!isset($data['amount'])) {
            throw new \InvalidArgumentException('Argument amount is required');
        }
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new ValidatorException(__('Amount must be a positive number'));
        }

        /** @var Quote $quote */
        $quote = $data['quote'];
        if (!$quote->getId()) {
            $this->cartRepository->save($quote);
        }

        $this->cartManagement->addBalanceUnitToCart(
            $quote->getId(),
            $data['amount']
        );

        return true;
    }
}
