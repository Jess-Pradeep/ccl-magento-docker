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
namespace Aheadworks\Ctq\Plugin\Controller\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Checkout\Controller\Cart\UpdatePost;
use Magento\Checkout\Helper\Cart as CartHelper;
use Aheadworks\Ctq\Model\Request\Checker;
use Aheadworks\Ctq\Model\QuoteList\State;

/**
 * Class UpdatePostPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Controller\Cart
 */
class UpdatePostPlugin
{
    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var State
     */
    private $state;

    /**
     * @var CartHelper
     */
    private $cartHelper;

    /**
     * UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Checker $checker
     * @param State $state
     * @param CartHelper $cartHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Checker $checker,
        State $state,
        CartHelper $cartHelper,
        UrlInterface $urlBuilder
    ) {
        $this->checker = $checker;
        $this->state = $state;
        $this->cartHelper = $cartHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Emulate quote list if needed
     *
     * @param UpdatePost $subject
     * @param \Closure $proceed
     * @return ResultRedirect
     * @throws LocalizedException
     */
    public function aroundExecute($subject, \Closure $proceed)
    {
        if ($this->checker->isQuoteList()) {
            /** @var ResultRedirect $result */
            $result = $this->state->emulateQuote($proceed);
            if ($this->cartHelper->getShouldRedirectToCart() || $subject->getRequest()->getParam('in_cart')) {
                $result->setUrl($this->urlBuilder->getUrl('aw_ctq/quoteList'));
            }

            return $result;
        }

        return $proceed();
    }
}
