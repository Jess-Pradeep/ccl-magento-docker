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
namespace Aheadworks\CreditLimit\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class BalanceUpdate
 *
 * @package Aheadworks\CreditLimit\Block\Customer
 * @method \Aheadworks\CreditLimit\ViewModel\Customer\PaymentForm getViewModel()
 */
class BalanceUpdate extends Template
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param CustomerManagementInterface $customerManagement
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerManagementInterface $customerManagement,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->customerManagement = $customerManagement;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _toHtml()
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        if (!$this->customerManagement->isAllowedToUpdateCreditBalance($this->getCustomerId(), $websiteId)) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getViewModel()->prepareJsLayout($this->jsLayout, $this->getCustomerId());
        return parent::getJsLayout();
    }

    /**
     * Retrieve customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}
