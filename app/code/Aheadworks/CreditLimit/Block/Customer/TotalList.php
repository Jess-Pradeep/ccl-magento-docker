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
use Aheadworks\CreditLimit\Model\Customer\Layout\Processor\TotalList as TotalListProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\CreditLimit\Api\PaymentPeriodManagementInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class TotalList
 *
 * @package Aheadworks\CreditLimit\Block\Customer
 */
class TotalList extends Template
{
    /**
     * @var TotalListProcessor
     */
    private $totalListProcessor;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * TotalList constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param TotalListProcessor $totalListProcessor
     * @param JsonSerializer $jsonSerializer
     * @param PaymentPeriodManagementInterface $paymentPeriodManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        TotalListProcessor $totalListProcessor,
        JsonSerializer $jsonSerializer,
        private PaymentPeriodManagementInterface $paymentPeriodManagement,
        private ArrayManager $arrayManager,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->totalListProcessor = $totalListProcessor;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * Prepare JS layout of block
     *
     * @throws LocalizedException
     */
    public function getJsLayout()
    {
        $customerId = $this->getCustomerId();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $this->jsLayout = $this->totalListProcessor->process($this->jsLayout, $customerId, $websiteId);

        $modifyLayoutData = $this->getModifyLayout((int)$customerId);
        return $this->jsonSerializer->serialize($modifyLayoutData);
    }

    /**
     * Apply sort attributes to layout items
     *
     * @param int $customerId
     * @return array
     */
    private function getModifyLayout(int $customerId): array
    {
        $modifyData = $this->jsLayout;
        $dueDate = $this->paymentPeriodManagement->getDueDate($customerId);
        if ($dueDate) {
            $dueDatePath = $this->arrayManager->findPath('due_date', $modifyData);
            $creditTermsPath = $this->arrayManager->findPath('payment_period', $modifyData);
            if ($dueDatePath && $creditTermsPath) {
                $mergeData['isMove'] = true;
                $modifyData = $this->arrayManager->merge($dueDatePath, $modifyData, $mergeData);
                $modifyData = $this->arrayManager->merge($creditTermsPath, $modifyData, $mergeData);
            }
        }
        return $modifyData;
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
