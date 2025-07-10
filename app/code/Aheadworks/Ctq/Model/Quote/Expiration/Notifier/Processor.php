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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Expiration\Notifier;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder\EmailVariables;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\Quote\Expiration\Notifier\VariableProcessor\Composite as VariableProcessorComposite;
use Aheadworks\Ctq\Model\History\Notifier\RecipientResolver;

/**
 * Class Processor
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration\Notifier
 */
class Processor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    private $variableProcessorComposite;

    /**
     * @var RecipientResolver
     */
    private $recipientResolver;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     * @param RecipientResolver $recipientResolver
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite,
        RecipientResolver $recipientResolver
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->recipientResolver = $recipientResolver;
    }

    /**
     * Process
     *
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface
     */
    public function process($quote)
    {
        $storeId = $quote->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($quote))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->recipientResolver->resolveBuyerName($quote))
            ->setRecipientEmail($this->recipientResolver->resolveBuyerEmail($quote))
            ->setCc($this->getCc($quote));

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    private function getTemplateId($storeId)
    {
        return $this->config->getExpirationReminderTemplate($storeId);
    }

    /**
     * Retrieve cc
     *
     * @param QuoteInterface $quote
     * @return string|null
     */
    private function getCc($quote)
    {
        return $quote->getCcEmailReceiver();
    }

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     */
    private function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    private function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param QuoteInterface $quote
     * @return array
     */
    private function prepareTemplateVariables($quote)
    {
        $templateVariables = [
            EmailVariables::QUOTE => $quote,
            EmailVariables::QUOTE_ID => $quote->getId(),
            EmailVariables::STORE_ID => $quote->getStoreId(),
            EmailVariables::CUSTOMER_NAME => $this->recipientResolver->resolveBuyerName($quote)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}
