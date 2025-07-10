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
namespace Aheadworks\Ctq\Controller;

use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;

/**
 * Class ExternalAction
 *
 * @package Aheadworks\Ctq\Controller
 */
abstract class ExternalAction extends AbstractAction
{
    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param Context $context
     * @param ItemsChecker $itemsChecker
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        ItemsChecker $itemsChecker,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context, $itemsChecker);
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Redirect from guest quote with customer ID to customer account
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        $quote = $this->getQuoteByHash();
        if ($quote->getCustomerId()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            /** @var ResultRedirect|ResponseInterface $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('aw_ctq/quote/edit', ['quote_id' => $quote->getId()]);

            return $resultRedirect;
        }

        return parent::dispatch($request);
    }

    /**
     * Get quote by hash
     *
     * @return QuoteInterface
     * @throws LocalizedException
     */
    protected function getQuoteByHash()
    {
        try {
            $quoteHash = $this->getRequest()->getParam('hash');
            $requestEntity = $this->quoteRepository->getByHash($quoteHash);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $requestEntity;
    }
}
