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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\PaymentReminder\Notifier;

use Magento\Framework\App\Area;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\CreditLimit\Model\Config;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterface;
use Aheadworks\CreditLimit\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\CreditLimit\Model\Email\VariableProcessorInterface;
use Aheadworks\CreditLimit\Model\Source\Customer\EmailVariables;

class EmailProcessor
{
    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Config $config
     * @param CustomerRepositoryInterface $customerRepository
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorInterface $variableProcessorComposite
     */
    public function __construct(
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly Config $config,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly EmailMetadataInterfaceFactory $emailMetadataFactory,
        private readonly VariableProcessorInterface $variableProcessorComposite
    ) {
    }

    /**
     *
     *
     * @param SummaryInterface $summary
     * @return EmailMetadataInterface
     * @throws \Exception
     */
    public function process(SummaryInterface $summary): EmailMetadataInterface
    {
        $customer = $this->customerRepository->getById($summary->getCustomerId());
        $storeId = (int)$customer->getStoreId();

        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($customer, $summary))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($customer))
            ->setRecipientEmail($this->getRecipientEmail($customer));

        return $emailMetaData;
    }

    /**
     * Retrieve template ID
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId(int $storeId): string
    {
        return $this->config->getCreditBalanceReminderTemplate($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions(int $storeId): array
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param CustomerInterface $customer
     * @param SummaryInterface $summary
     * @return array
     */
    private function prepareTemplateVariables(CustomerInterface $customer, SummaryInterface $summary): array
    {
        $templateVariables = [
            EmailVariables::CUSTOMER => $this->dataObjectProcessor->buildOutputDataArray(
                $customer,
                CustomerInterface::class
            ),
            EmailVariables::SUMMARY => $this->dataObjectProcessor->buildOutputDataArray(
                $summary,
                SummaryInterface::class
            ),
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderName(int $storeId): string
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderEmail(int $storeId): string
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getRecipientName(CustomerInterface $customer): string
    {
        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }

    /**
     * Retrieve recipient email
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getRecipientEmail(CustomerInterface $customer): string
    {
        return $customer->getEmail();
    }
}
