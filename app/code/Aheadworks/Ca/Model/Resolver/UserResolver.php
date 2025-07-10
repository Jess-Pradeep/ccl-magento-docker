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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Resolver;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ca\Model\User\UserRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UserResolver
 */
class UserResolver
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param UserContextInterface $userContext
     * @param UserRepository $userRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        UserContextInterface $userContext,
        UserRepository $userRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->userContext = $userContext;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get User Name
     *
     * @param int $userId
     * @return string
     * @throws LocalizedException
     */
    public function getUserName(int $userId): string
    {
        $userName = '';
        try {
            if ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
                $user = $this->userRepository->getById($userId);
                $userName = $user->getFirstName() . ' ' . $user->getLastName();
            } elseif ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER) {
                $customer = $this->customerRepository->getById($userId);
                $userName = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
        } catch (NoSuchEntityException $exception) {
        }

        return $userName;
    }

    /**
     * Get User Id
     *
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->userContext->getUserId();
    }

    /**
     * Check is user admin
     *
     * @return bool
     */
    public function isUserAdmin(): bool
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_ADMIN;
    }

    /**
     * Check is user customer
     *
     * @return bool
     */
    public function isUserCustomer(): bool
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER;
    }
}
