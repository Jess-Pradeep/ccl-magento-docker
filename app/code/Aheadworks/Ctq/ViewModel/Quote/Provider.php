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

namespace Aheadworks\Ctq\ViewModel\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterfaceFactory;

/**
 * Provides quote
 */
class Provider implements ArgumentInterface
{
    /**
     * @var QuoteInterfaceFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param QuoteInterfaceFactory $quoteFactory
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        QuoteInterfaceFactory $quoteFactory,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Get quote
     *
     * @param int $quoteId
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function getQuote(int $quoteId): QuoteInterface
    {
        return $this->quoteRepository->get($quoteId, true);
    }

    /**
     * Get empty quote
     *
     * @return QuoteInterface
     */
    public function getEmptyQuote() : QuoteInterface
    {
        return $this->quoteFactory->create();
    }
}
