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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\Company\Notifier\EmailProcessor;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\VariableProcessorInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Email\EmailMetadataInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

abstract class AbstractProcessor implements EmailProcessorInterface
{
    /**
     * @var array|CustomerInterface[]
     */
    protected array $rootCustomer = [];

    /**
     * @param Config $config
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     */
    public function __construct(
        protected Config $config,
        protected CompanyUserManagementInterface $companyUserManagement,
        protected StoreManagerInterface $storeManager,
        protected EmailMetadataInterfaceFactory $emailMetadataFactory,
        protected VariableProcessorInterface $variableProcessorComposite
    ) {
    }

    /**
     * @inheritdoc
     */
    public function process($company)
    {
        $storeId = $this->getRootCustomer($company)->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($company, $storeId))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($company))
            ->setRecipientEmail($this->getRecipientEmail($company));

        return [$emailMetaData];
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    protected function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param CompanyInterface $company
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    protected function prepareTemplateVariables($company, $storeId)
    {
        $templateVariables = [
            EmailVariables::COMPANY => $company,
            EmailVariables::STORE => $this->storeManager->getStore($storeId),
            EmailVariables::CUSTOMER => $this->getRootCustomer($company)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    protected function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Retrieve root customer
     *
     * @param CompanyInterface $company
     * @return CustomerInterface
     */
    protected function getRootCustomer(CompanyInterface $company): CustomerInterface
    {
        if (empty($this->rootCustomer[$company->getId()])) {
            $this->rootCustomer[$company->getId()] = $this->companyUserManagement->getRootUserForCompany($company->getId());
        }
        return $this->rootCustomer[$company->getId()];
    }

    /**
     * Retrieve recipient name
     *
     * @param CompanyInterface $company
     * @return string
     */
    abstract protected function getRecipientName($company);

    /**
     * Retrieve recipient email
     *
     * @param CompanyInterface $company
     * @return string
     */
    abstract protected function getRecipientEmail($company);

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    abstract protected function getTemplateId($storeId);
}
