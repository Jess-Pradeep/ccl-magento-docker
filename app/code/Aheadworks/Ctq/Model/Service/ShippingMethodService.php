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
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Aheadworks\Ctq\Api\ShipmentEstimationInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingMethodService implements ShipmentEstimationInterface
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var QuoteListManagementInterface
     */
    private $quoteListManagement;

    /**
     * @var \Magento\Quote\Api\ShipmentEstimationInterface
     */
    private $shippingMethodManagement;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param QuoteListManagementInterface $quoteListManagement
     * @param \Magento\Quote\Api\ShipmentEstimationInterface $shippingMethodManagement
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        QuoteListManagementInterface $quoteListManagement,
        \Magento\Quote\Api\ShipmentEstimationInterface $shippingMethodManagement

    ) {
        $this->userContext = $userContext;
        $this->quoteListManagement = $quoteListManagement;
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

    /**
     * @inerhitDoc
     */
    public function estimateByExtendedAddress(\Magento\Quote\Api\Data\AddressInterface $address): array
    {
        try {
            if ($this->userContext->getUserType() === \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();

                $quote = $this->quoteListManagement->getQuoteListForCustomer($customerId);
                if ($quote) {
                    return $this->shippingMethodManagement->estimateByExtendedAddress((int)$quote->getId(), $address);
                }
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Current customer does not have an active quote list.'));
        }
        return [];
    }
}