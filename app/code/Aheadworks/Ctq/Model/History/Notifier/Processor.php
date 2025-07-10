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
namespace Aheadworks\Ctq\Model\History\Notifier;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Config;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterface;
use Aheadworks\Ctq\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\Ctq\Model\Source\History\Action\Type;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Aheadworks\Ctq\Model\Source\History\Status;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Model\History\Notifier\VariableProcessor\Composite as VariableProcessorComposite;
use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Aheadworks\Ctq\Model\Source\Quote\Status as QuoteStatus;

/**
 * Class Processor
 *
 * @package Aheadworks\Ctq\Model\History\Notifier
 */
class Processor
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    protected $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    protected $variableProcessorComposite;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RecipientResolver
     */
    protected $recipientResolver;

    /**
     * @var AttachmentProvider
     */
    protected $attachmentProvider;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param VariableProcessorComposite $variableProcessorComposite
     * @param UserRepository $userRepository
     * @param RecipientResolver $recipientResolver
     * @param AttachmentProvider $attachmentProvider
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        VariableProcessorComposite $variableProcessorComposite,
        UserRepository $userRepository,
        RecipientResolver $recipientResolver,
        AttachmentProvider $attachmentProvider
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->variableProcessorComposite = $variableProcessorComposite;
        $this->userRepository = $userRepository;
        $this->recipientResolver = $recipientResolver;
        $this->attachmentProvider = $attachmentProvider;
    }

    /**
     * Process
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface[]
     */
    public function process($history, $quote)
    {
        $emailMetaDataObjects = [];
        if ($this->isNewQuote($history)) {
            $emailMetaDataObjects = $this->processForBoth($history, $quote);
        } else {
            $emailMetaDataObjects[] = $this->processForSingle($history, $quote);
        }

        return $emailMetaDataObjects;
    }

    /**
     * Process for both
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface[]
     */
    protected function processForBoth($history, $quote)
    {
        $recipients = [Owner::SELLER => $quote->getSellerId(), Owner::BUYER => $quote->getCustomerId()];
        $emailMetaDataObjects = [];
        $historyMod = clone $history;
        foreach ($recipients as $ownerType => $recipientId) {
            $historyMod
                ->setOwnerType($ownerType)
                ->setOwnerId($recipientId);

            $emailMetaDataObjects[] = $this->processForSingle($historyMod, $quote);
        }

        return $emailMetaDataObjects;
    }

    /**
     * Process for single
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    protected function processForSingle($history, $quote)
    {
        $storeId = $quote->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($history, $quote))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($history, $quote))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($history, $quote))
            ->setRecipientEmail($this->getRecipientEmail($history, $quote))
            ->setCc($this->getCc($history, $quote))
            ->setAttachments($this->getAttachments($history, $quote));

        // set bcc to seller if quote expired
        if (count($history->getActions()) == 1) {
            /** @var HistoryActionInterface $historyAction */
            $historyAction = current($history->getActions());
            if ($historyAction->getType() == Type::QUOTE_ATTRIBUTE_STATUS
                && $historyAction->getValue() == QuoteStatus::EXPIRED
            ) {
                $emailMetaData->setBcc($this->getBcc($quote));
            }
        }

        return $emailMetaData;
    }

    /**
     * Check if new quote
     *
     * @param HistoryInterface $history
     * @return bool
     */
    protected function isNewQuote($history)
    {
        return $history->getStatus() == Status::CREATED_QUOTE;
    }

    /**
     * Retrieve template id
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getTemplateId($history, $quote)
    {
        $storeId = $quote->getStoreId();
        $isNewQuote = $this->isNewQuote($history);
        if ($this->isNotifierForSeller($history)) {
            $template = $isNewQuote
                ? $this->config->getSellerNewQuoteTemplate($storeId)
                : $this->config->getSellerQuoteChangesTemplate($storeId);
        } else {
            $template = $isNewQuote
                ? $this->config->getBuyerNewQuoteTemplate($storeId)
                : $this->config->getBuyerQuoteChangesTemplate($storeId);
        }

        return $template;
    }

    /**
     * Retrieve recipient name
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientName($history, $quote)
    {
        if ($this->isNotifierForSeller($history)) {
            $name = $this->recipientResolver->resolveSellerName($quote);
        } else {
            $name = $this->recipientResolver->resolveBuyerName($quote);
        }

        return $name;
    }

    /**
     * Retrieve recipient email
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string
     */
    protected function getRecipientEmail($history, $quote)
    {
        try {
            if ($this->isNotifierForSeller($history)) {
                $email = $this->recipientResolver->resolveSellerEmail($quote);
            } else {
                $email = $this->recipientResolver->resolveBuyerEmail($quote);
            }
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }

    /**
     * Retrieve cc
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return string|null
     */
    protected function getCc($history, $quote)
    {
        $email = null;
        if ($this->isNotifierForSeller($history)) {
            $storeId = $quote->getStoreId();
            $email = $this->config->getRecipientsEmail($storeId);
        } else {
            $email = $quote->getCcEmailReceiver();
        }
        return $email;
    }

    /**
     * Retrieve bcc
     *
     * @param QuoteInterface $quote
     * @return array
     */
    protected function getBcc($quote)
    {
        $emails = [];

        /** @noinspection PhpUnhandledExceptionInspection */
        try {
            $user = $this->userRepository->getById($quote->getSellerId());
            $emails[] = $user->getEmail();
        } catch (\Exception $e) {
        }

        $storeId = $quote->getStoreId();
        $emails = array_merge($emails, $this->config->getRecipientsEmail($storeId));

        return $emails;
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
     * Check if notifier for seller
     *
     * @param HistoryInterface $history
     * @return bool
     */
    protected function isNotifierForSeller($history)
    {
        return $history->getOwnerType() == Owner::BUYER;
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
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return array
     */
    protected function prepareTemplateVariables($history, $quote)
    {
        $templateVariables = [
            EmailVariables::HISTORY_ID => $history->getId(),
            EmailVariables::HISTORY => $history,
            EmailVariables::QUOTE => $quote,
            EmailVariables::QUOTE_ID => $quote->getId(),
            EmailVariables::STORE => $this->storeManager->getStore($quote->getStoreId()),
            EmailVariables::STORE_ID => $quote->getStoreId(),
            EmailVariables::USER_NAME => $this->getRecipientName($history, $quote),
            EmailVariables::IS_SELLER => $this->isNotifierForSeller($history),
            EmailVariables::BUYER_NAME => $this->recipientResolver->resolveBuyerName($quote)
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }

    /**
     * Prepare attachments
     *
     * @param HistoryInterface $history
     * @param QuoteInterface $quote
     * @return array
     * @throws LocalizedException
     */
    protected function getAttachments($history, $quote)
    {
        $attachments = [];
        if (!$this->config->isPdfAttachedToEmail()
            || !is_array($history->getActions())
            || $history->getOwnerType() != Owner::SELLER
        ) {
            return $attachments;
        }

        foreach ($history->getActions() as $historyAction) {
            if ($historyAction->getType() == Type::QUOTE_ATTRIBUTE_STATUS
                && $historyAction->getOldValue() == QuoteStatus::PENDING_SELLER_REVIEW
                && $historyAction->getValue() == QuoteStatus::PENDING_BUYER_REVIEW
            ) {
                $attachments[] = $this->attachmentProvider->getPdfAttachmentForQuote($quote);
            }
        }

        return $attachments;
    }
}
