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
namespace Aheadworks\Ctq\Plugin\Model\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ctq\Model\ResourceModel\Quote as QuoteResourceModel;

/**
 * Class AccountManagementPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Model\Customer
 */
class AccountManagementPlugin
{
    /**
     * @var QuoteResourceModel
     */
    private $quoteResourceModel;

    /**
     * @param QuoteResourceModel $quoteResourceModel
     */
    public function __construct(
        QuoteResourceModel $quoteResourceModel
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
    }

    /**
     * Update all guest quotes related to newly registered customer
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $resultCustomer
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function afterCreateAccountWithPasswordHash(AccountManagementInterface $subject, $resultCustomer)
    {
        $this->quoteResourceModel->setCustomerIdToGuestQuotes($resultCustomer->getId(), $resultCustomer->getEmail());
        return $resultCustomer;
    }
}
