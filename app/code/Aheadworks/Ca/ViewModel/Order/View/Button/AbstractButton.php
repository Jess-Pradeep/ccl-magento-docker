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
namespace Aheadworks\Ca\ViewModel\Order\View\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Url as UrlModel;
use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;

/**
 * Class AbstractButton
 *
 * @package Aheadworks\Ca\ViewModel\Order\View\Button
 */
abstract class AbstractButton implements ArgumentInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var UrlModel
     */
    protected $url;

    /**
     * @var CompanyUserProvider
     */
    protected $companyUserProvider;

    /**
     * @var IsActiveChecker
     */
    protected $isActiveChecker;

    /**
     * @param Registry $registry
     * @param UrlModel $url
     * @param CompanyUserProvider $companyUserProvider
     * @param IsActiveChecker $isActiveChecker
     */
    public function __construct(
        Registry $registry,
        UrlModel $url,
        CompanyUserProvider $companyUserProvider,
        IsActiveChecker $isActiveChecker
    ) {
        $this->registry = $registry;
        $this->url = $url;
        $this->companyUserProvider = $companyUserProvider;
        $this->isActiveChecker = $isActiveChecker;
    }
    
    /**
     * Check if button is visible
     *
     * @return bool
     */
    public function isButtonVisible()
    {
        $order = $this->getOrder();
        return $this->isActiveChecker->isOrderUnderApprovalConsideration($order)
            && $this->companyUserProvider->isCurrentCompanyUserRoot();
    }

    /**
     * Get order
     *
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get submit url
     *
     * @return string
     */
    abstract public function getSubmitUrl();

    /**
     * Get button title
     *
     * @return string
     */
    abstract public function getButtonTitle();
}
