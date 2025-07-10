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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\History\Notifier;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;

/**
 * Class RecipientResolver
 *
 * @package Aheadworks\Ctq\Model\History\Notifier
 */
class RecipientResolver
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserRepository $userRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Resolve buyer name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function resolveBuyerName($quote)
    {
        try {
            if ($quote->getCustomerId()) {
                $customer = $this->customerRepository->getById($quote->getCustomerId());
                $name = $customer->getFirstname() . ' ' .  $customer->getLastname();
            } else {
                $name = $quote->getCustomerFirstName() . ' ' . $quote->getCustomerLastName();
            }

        } catch (\Exception $e) {
            $name = '';
        }

        return $name;
    }

    /**
     * Resolve buyer email
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function resolveBuyerEmail($quote)
    {
        try {
            if ($quote->getCustomerId()) {
                $customer = $this->customerRepository->getById($quote->getCustomerId());
                $email = $customer->getEmail();
            } else {
                $email = $quote->getCustomerEmail();
            }
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }

    /**
     * Resolve seller name
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function resolveSellerName($quote)
    {
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }
        return $name;
    }

    /**
     * Resolve seller email
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function resolveSellerEmail($quote)
    {
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $email = $user->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }
        return $email;
    }
}
