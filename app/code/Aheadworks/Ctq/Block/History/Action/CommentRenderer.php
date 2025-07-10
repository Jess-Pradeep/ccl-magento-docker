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
namespace Aheadworks\Ctq\Block\History\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Comment as CommentViewModel;
use Aheadworks\Ctq\ViewModel\Customer\Quote\External\Comment as ExternalCommentViewModel;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;

/**
 * Comment history action renderer
 *
 * @method CommentViewModel getCommentViewModel()
 * @method ExternalCommentViewModel getExternalCommentViewModel()
 */
class CommentRenderer extends DefaultRenderer
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Resolve proper comment view model
     *
     * @param int $quoteId
     * @param bool $isEmailForSeller
     * @return CommentViewModel|ExternalCommentViewModel
     * @throws NoSuchEntityException
     */
    public function resolveCommentViewModel($quoteId, $isEmailForSeller)
    {
        if (!$isEmailForSeller) {
            $quote = $this->quoteRepository->get($quoteId);
            if (!$quote->getCustomerId()) {
                return $this->getExternalCommentViewModel();
            }
        }

        return $this->getCommentViewModel();
    }
}
