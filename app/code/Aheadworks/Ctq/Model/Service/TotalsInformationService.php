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

namespace Aheadworks\Ctq\Model\Service;

use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class TotalsInformationService implements \Aheadworks\Ctq\Api\TotalsInformationManagementInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var QuoteListManagementInterface
     */
    private $quoteListManagement;

    /**
     * @var \Magento\Checkout\Api\TotalsInformationManagementInterface
     */
    private $totalsInformationManagement;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param QuoteListManagementInterface $quoteListManagement
     * @param \Magento\Quote\Api\ShipmentEstimationInterface $totalsInformationManagement
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        QuoteListManagementInterface $quoteListManagement,
        \Magento\Checkout\Api\TotalsInformationManagementInterface $totalsInformationManagement

    ) {
        $this->userContext = $userContext;
        $this->quoteListManagement = $quoteListManagement;
        $this->totalsInformationManagement = $totalsInformationManagement;
    }

    /**
     * @inerhitDoc
     */
    public function calculate(\Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation):? \Magento\Quote\Api\Data\TotalsInterface
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();

                $quote = $this->quoteListManagement->getQuoteListForCustomer($customerId);
                if ($quote) {
                    return $this->totalsInformationManagement->calculate((int)$quote->getId(), $addressInformation);
                }
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Current customer does not have an active quote list.'));
        }
        return null;
    }
}