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
namespace Aheadworks\Ctq\Model\Layout\Customer\Quote\RequestPopup\Processor;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Ctq\Model\Layout\ProcessorInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class QuoteRestrictionMessage
 */
class QuoteRestrictionMessage implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ArrayManager $arrayManager
     * @param CartRepositoryInterface $cartRepository
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        CartRepositoryInterface $cartRepository,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $quoteLinkPath = $this->arrayManager->findPath('awCtqRequestQuoteLink', $jsLayout);
        $store = $this->storeManager->getStore()->getId();
        if ($quoteLinkPath) {
            $jsLayout = $this->arrayManager->merge(
                $quoteLinkPath,
                $jsLayout,
                [
                    'config' => [
                        'isAllowed' => $this->isShowModal($jsLayout),
                        'minQuoteSubtotalMessage' => __($this->config->getMinimumQuoteSubtotalMessage($store))
                    ]
                ]
            );
        }

        if (!$this->isShowModal($jsLayout)) {
            $awCtqRequestQuote = $this->arrayManager->findPath('awCtqRequestQuote', $jsLayout);
            if ($awCtqRequestQuote) {
                $jsLayout = $this->arrayManager->remove($awCtqRequestQuote, $jsLayout);
            }
        }

        return $jsLayout;
    }

    /**
     * Is show modal
     *
     * @param array $jsLayout
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function isShowModal($jsLayout)
    {
        $result = false;

        if (isset($jsLayout['quote_id'])) {
            $quote = $this->cartRepository->getActive($jsLayout['quote_id']);
            $result = $quote->getBaseSubtotal() >= $this->config->getMinimumQuoteSubtotal();
        }

        return $result;
    }
}
