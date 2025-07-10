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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Export;

use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf as PdfExporter;

/**
 * Class Pdf
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote\Export
 */
class Pdf extends BackendAction
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var PdfExporter
     */
    private $pdfExporter;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param PdfExporter $pdfExporter
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        PdfExporter $pdfExporter
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->pdfExporter = $pdfExporter;
    }

    /**
     * Export to pdf action
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $quoteId = (int)$this->getRequest()->getParam(QuoteInterface::ID);
        $quote = $this->quoteRepository->get($quoteId);
        return $this->pdfExporter->exportQuote($quote);
    }
}
