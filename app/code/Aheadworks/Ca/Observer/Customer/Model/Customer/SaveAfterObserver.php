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
namespace Aheadworks\Ca\Observer\Customer\Model\Customer;

use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveAfterObserver
 */
class SaveAfterObserver implements ObserverInterface
{
    /**
     * @param CommandInterface $saveCompanyUserAdditionalInfoFromCustomer
     */
    public function __construct(
        private readonly CommandInterface $saveCompanyUserAdditionalInfoFromCustomer
    ) {
    }

    /**
     * Save company user additional info
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        $data = $event->getData('request')->getPostValue();
        $this->saveCompanyUserAdditionalInfoFromCustomer->execute($data);
    }
}
