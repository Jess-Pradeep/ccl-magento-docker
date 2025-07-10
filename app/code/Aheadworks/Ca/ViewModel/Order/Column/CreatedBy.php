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
namespace Aheadworks\Ca\ViewModel\Order\Column;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CreatedBy
 * @package Aheadworks\Ca\ViewModel\Order\Column
 */
class CreatedBy extends Column implements ArgumentInterface
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HttpContext $httpContext
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HttpContext $httpContext,
        array $components = [],
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Retrieve customer name from order
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getCreatedBy($order)
    {
        return $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
    }

    /**
     * Check if need to add column
     *
     * @return bool
     */
    public function needToAddColumn()
    {
        $companyInfo = $this->httpContext->getValue('company_info');
        return (bool)$companyInfo[CompanyUserInterface::COMPANY_ID];
    }
}
