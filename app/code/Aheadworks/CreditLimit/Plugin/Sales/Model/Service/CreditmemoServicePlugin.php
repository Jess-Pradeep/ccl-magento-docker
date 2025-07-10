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
namespace Aheadworks\CreditLimit\Plugin\Sales\Model\Service;

use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Api\Data\CreditmemoInterface;

/**
 * Class CreditmemoServicePlugin
 */
class CreditmemoServicePlugin
{
    /**
     * @var CreditLimitManagementInterface
     */
    private $creditManagement;

    /**
     * @param CreditLimitManagementInterface $creditManagement
     */
    public function __construct(
        CreditLimitManagementInterface $creditManagement
    ) {
        $this->creditManagement = $creditManagement;
    }

    /**
     * Decrease customer credit balance by refunding balance unit product
     *
     * @param CreditmemoService $subject
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRefund(CreditmemoService $subject, CreditmemoInterface $creditmemo)
    {
        if ($creditmemo->getCustomerId()) {
            $this->creditManagement->decreaseCreditBalanceByUnitRefund($creditmemo->getCustomerId(), $creditmemo);
        }

        return $creditmemo;
    }
}
