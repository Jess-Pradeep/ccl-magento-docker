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

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf as PdfExporter;
use Aheadworks\Ctq\Model\Email\AttachmentInterfaceFactory;
use Aheadworks\Ctq\Model\Email\AttachmentInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;

/**
 * Class AttachmentProvider
 *
 * @package Aheadworks\Ctq\Model\History\Notifier
 */
class AttachmentProvider
{
    /**
     * @var PdfExporter
     */
    private $pdfExporter;

    /**
     * @var AttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @param PdfExporter $pdfExporter
     * @param AttachmentInterfaceFactory $attachmentFactory
     */
    public function __construct(
        PdfExporter $pdfExporter,
        AttachmentInterfaceFactory $attachmentFactory
    ) {
        $this->pdfExporter = $pdfExporter;
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * Get pdf attachment for quote
     *
     * @param QuoteInterface $quote
     * @return AttachmentInterface
     * @throws LocalizedException
     */
    public function getPdfAttachmentForQuote($quote)
    {
        /** @var AttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $this->disableQuoteSaving($quote);
        $attachment
            ->setContent($this->pdfExporter->prepareFileContent($quote, Locator::LOCATE_BY_ID))
            ->setFileName($this->pdfExporter->prepareFileName($quote));

        return $attachment;
    }

    /**
     * Put flag to skip quote saving in management class
     *
     * @param QuoteInterface $quote
     */
    private function disableQuoteSaving($quote)
    {
        $cart = $quote->getCart();
        $nativeQuote = $cart->getQuote();
        $nativeQuote['aw_ctq_skip_quote_saving'] = true;
        $cart->setQuote($nativeQuote);
        $quote->setCart($cart);
    }
}
