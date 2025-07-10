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
namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;

/**
 * Class Locator
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class Locator
{
    const LOCATE_BY_ID = 'locate_by_id';
    const LOCATE_BY_HASH = 'locate_by_hash';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteInterfaceFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $quoteRepository,
        QuoteInterfaceFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        BuyerQuoteManagementInterface $buyerQuoteManagement
    ) {
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * Retrieve active quote or create a new one
     *
     * @param string $method
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function locateQuote($method = self::LOCATE_BY_ID)
    {
        try {
            switch ($method) {
                case self::LOCATE_BY_ID:
                    $quoteId = $this->request->getParam('quote_id');
                    if (!$quoteId) {
                        $quoteId = $this->request->getParam('id');
                    }
                    $quote = $this->quoteRepository->get($quoteId);
                    break;
                case self::LOCATE_BY_HASH:
                    $quoteHash = $this->request->getParam('hash');
                    $quote = $this->quoteRepository->getByHash($quoteHash);
                    break;
                default:
                    $quote = null;

            }
        } catch (NoSuchEntityException $exception) {
            $quote = null;
        }
        if ($quote) {
            //to additionally initialize quote
            $this->getCartByQuote($quote);
        } else {
            /** @var QuoteInterface $quote */
            $quote = $this->quoteFactory->create();
        }

        return $quote;
    }

    /**
     * Get cart by quote
     *
     * @param QuoteInterface $quote
     * @return CartInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCartByQuote($quote)
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->buyerQuoteManagement->getCartByQuote($quote, $storeId);
    }
}
