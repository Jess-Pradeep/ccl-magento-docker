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
namespace Aheadworks\Ctq\Model\Quote\Export\Exporter;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\LayoutInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Export\ExporterInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;
use Aheadworks\Ctq\Model\Filesystem\FileResponseFactory;
use Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf\Document as PdfDocument;
use Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf\DocumentFactory as PdfDocumentFactory;

class Pdf implements ExporterInterface
{
    /**
     * @var FileResponseFactory
     */
    private $fileResponseFactory;

    /**
     * @var LayoutInterfaceFactory
     */
    private $layoutFactory;

    /**
     * @var PdfDocumentFactory
     */
    private $pdfDocumentFactory;

    /**
     * @param FileResponseFactory $fileResponseFactory
     * @param LayoutInterfaceFactory $layoutFactory
     * @param PdfDocumentFactory $pdfDocumentFactory
     */
    public function __construct(
        FileResponseFactory $fileResponseFactory,
        LayoutInterfaceFactory $layoutFactory,
        PdfDocumentFactory $pdfDocumentFactory
    ) {
        $this->fileResponseFactory = $fileResponseFactory;
        $this->layoutFactory = $layoutFactory;
        $this->pdfDocumentFactory = $pdfDocumentFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function exportQuote($quote, $methodToLocate = Locator::LOCATE_BY_ID)
    {
        return $this->fileResponseFactory->create(
            $this->prepareFileContent($quote, $methodToLocate),
            $this->prepareFileName($quote)
        );
    }

    /**
     * Prepare file content
     *
     * @param QuoteInterface $quote
     * @param string $methodToLocate
     * @return string
     * @throws LocalizedException
     */
    public function prepareFileContent($quote, $methodToLocate = Locator::LOCATE_BY_ID)
    {
        $content = $this->getContent($quote, $methodToLocate);
        /** @var PdfDocument $pdfDocument */
        $pdfDocument = $this->pdfDocumentFactory->create();

        return $pdfDocument->createFromHtml($content);
    }

    /**
     * Prepare file name to export
     *
     * @param QuoteInterface $quote
     * @return string
     */
    public function prepareFileName($quote)
    {
        return 'quote #' . $quote->getId() . '.pdf';
    }

    /**
     * Retrieve content
     *
     * @param QuoteInterface $quote
     * @param string $methodToLocate
     * @return string
     * @throws LocalizedException
     */
    protected function getContent($quote, $methodToLocate)
    {
        /** @var $layout LayoutInterface */
        $layout = $this->layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->load('aw_ctq_export_quote_to_pdf');
        $layout->generateXml();
        $layout->generateElements();

        /** @var AbstractBlock $block */
        foreach ($layout->getAllBlocks() as $block) {
            $block->setData('quote', $quote);
            $block->setData('method_to_locate_quote', $methodToLocate);
        }

        return $layout->getOutput();
    }
}
