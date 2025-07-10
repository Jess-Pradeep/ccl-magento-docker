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
namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Cart\Purchase\LeaveCheckoutChecker;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Cleaner
{
    const COOKIE_NAME = 'section_data_ids';
    const CART_SECTION_NAME = 'cart';

    /**
     * @var LeaveCheckoutChecker
     */
    private $leaveCheckoutChecker;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var array
     */
    private $clearedCartIds = [];

    /**
     * @param Session $checkoutSession
     * @param LeaveCheckoutChecker $checker
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Json $serializer
     */
    public function __construct(
        Session $checkoutSession,
        LeaveCheckoutChecker $checker,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Json $serializer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->leaveCheckoutChecker = $checker;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->serializer = $serializer;
    }

    /**
     * Clear cart if customer leave quote checkout
     *
     * @param RequestInterface $request
     * @return void|null
     * @throws LocalizedException
     */
    public function clear($request)
    {
        if ($request->isAjax()) {
            return;
        }

        $cart = $this->checkoutSession->getQuote();
        $isLeave = $this->leaveCheckoutChecker->isLeave(
            $cart,
            $request
        );

        if (!in_array($cart->getId(), $this->clearedCartIds) && $isLeave) {
            $this->buyerQuoteManagement->clearCart($cart);
            $this->checkoutSession->clearQuote();
            $this->modifyCookie();
            $this->clearedCartIds[] = $cart->getId();
        }

        return null;
    }

    /**
     * Modify cookie
     */
    private function modifyCookie()
    {
        try {
            $cookieValue = $this->serializer->unserialize((string)$this->cookieManager->getCookie(self::COOKIE_NAME));
            $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(86400)
                ->setPath('/');

            $cookieValue[self::CART_SECTION_NAME] = 0;
            $this->cookieManager->setPublicCookie(
                self::COOKIE_NAME,
                $this->serializer->serialize($cookieValue),
                $cookieMetadata
            );
        } catch (\Exception $e) {
        }
    }
}
