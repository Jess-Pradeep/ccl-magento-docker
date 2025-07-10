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
namespace Aheadworks\CreditLimit\Controller\Balance;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\CreditLimit\Controller\Balance
 */
abstract class AbstractAction extends Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerManagementInterface
     */
    protected $customerManagement;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerManagementInterface $customerManagement
    ) {
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        $customerId = $this->customerSession->getCustomerId();
        $canUseCreditLimit = $this->customerManagement->isCreditLimitAvailable($customerId);
        if ($this->customerSession->authenticate() && !$canUseCreditLimit) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        return parent::dispatch($request);
    }
}
