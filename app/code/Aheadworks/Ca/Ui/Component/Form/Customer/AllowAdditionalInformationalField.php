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
namespace Aheadworks\Ca\Ui\Component\Form\Customer;

use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class AllowAdditionalInformationalField
 *
 * @package Aheadworks\Ca\Ui\Component\Form\Customer
 */
class AllowAdditionalInformationalField extends Field
{
    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CompanyUserProvider $companyUserProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CompanyUserProvider $companyUserProvider,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->companyUserProvider = $companyUserProvider;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->context->getRequestParam('id')) {
            $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($this->context->getRequestParam('id'));
            if ($companyUser) {
                $config = $this->getData('config');
                $config['componentDisabled'] = false;
                $this->setData('config', $config);
            }
        }
    }
}
