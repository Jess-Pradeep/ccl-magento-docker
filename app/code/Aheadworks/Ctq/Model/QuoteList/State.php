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
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Item\CommentApplier;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class State
 */
class State
{
    /**
     * @param Provider $provider
     * @param CheckoutSession $session
     * @param CommentApplier $commentApplier
     */
    public function __construct(
        private readonly Provider $provider,
        private readonly CheckoutSession $session,
        private readonly CommentApplier $commentApplier
    ) {}

    /**
     * Emulate environment with CTQ Quote instance in Checkout Session
     *
     * @param callable $callback
     * @param array $params
     * @return mixed
     * @throws LocalizedException
     */
    public function emulateQuote($callback, $params = [])
    {
        $currentQuote = $this->session->getQuote();
        $replaceQuote = $this->provider->getQuote();
        $replaceQuote = $this->commentApplier->apply($replaceQuote);
        $this->session->replaceQuote($replaceQuote);
        try {
            $result = call_user_func_array($callback, $params);
        } catch (\Exception $e) {
            $this->session->replaceQuote($currentQuote);
            throw $e;
        }
        $this->session->replaceQuote($currentQuote);

        return $result;
    }
}
