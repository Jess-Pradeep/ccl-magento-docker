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
namespace Aheadworks\Ctq\Model\Quote\EntityProcessor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Ctq\Model\Quote as QuoteModel;

/**
 * Class Customer
 *
 * @package Aheadworks\Ctq\Model\Quote\EntityProcessor
 */
class Customer
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Set customer ID for newly created quote
     *
     * @param QuoteModel $object
     * @return QuoteModel
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        if (!$object->getId() && !$object->getCustomerId()) {
            try {
                $customer = $this->customerRepository->get($object->getCustomerEmail());
                $customerId = $customer->getId();
            } catch (NoSuchEntityException $exception) {
                $customerId = null;
            }

            $object->setCustomerId($customerId);
        }

        return $object;
    }

    /**
     * After object load empty handler
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function afterLoad($object)
    {
        return $object;
    }
}
