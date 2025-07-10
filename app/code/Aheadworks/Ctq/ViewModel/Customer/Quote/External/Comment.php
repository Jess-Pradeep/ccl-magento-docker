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
namespace Aheadworks\Ctq\ViewModel\Customer\Quote\External;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Area;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Comment as QuoteComment;
use Aheadworks\Ctq\Model\Quote\Url;

/**
 * Class Comment
 *
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote\External
 */
class Comment extends QuoteComment
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param Url $url
     * @param TimezoneInterface $localeDate
     * @param QuoteRepositoryInterface $quoteRepository
     * @param string $area
     */
    public function __construct(
        Url $url,
        TimezoneInterface $localeDate,
        QuoteRepositoryInterface $quoteRepository,
        $area = Area::AREA_FRONTEND
    ) {
        parent::__construct($url, $localeDate, $area);
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Retrieve downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @param bool|null $isSeller
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDownloadUrl($attachmentFileName, $quoteId, $commentId, $isSeller = null)
    {
        $quote = $this->quoteRepository->get($quoteId);
        return $this->url->getFrontendDownloadExternalUrl(
            $attachmentFileName,
            $quote->getHash(),
            $commentId
        );
    }

    /**
     * @inheritdoc
     */
    public function getAddCommentUrl()
    {
        return $this->url->getAddCommentExternalUrl();
    }

    /**
     * @inheritdoc
     */
    public function isSubmitButtonDisplayed()
    {
        return true;
    }
}
