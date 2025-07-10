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

use Magento\Framework\UrlInterface;
use Magento\Framework\Url as FrontendUrl;
use Magento\Backend\Model\Url as BackendUrl;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class Url
 * @package Aheadworks\Ctq\Model\Quote
 */
class Url
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FrontendUrl
     */
    private $urlBuilderFrontend;

    /**
     * @var BackendUrl
     */
    private $urlBuilderBackend;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $urlBuilder
     * @param FrontendUrl $urlBuilderFrontend
     * @param BackendUrl $urlBuilderBackend
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlInterface $urlBuilder,
        FrontendUrl $urlBuilderFrontend,
        BackendUrl $urlBuilderBackend,
        StoreManagerInterface $storeManager
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->urlBuilderFrontend = $urlBuilderFrontend;
        $this->urlBuilderBackend = $urlBuilderBackend;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve quote url
     *
     * @param int $quoteId
     * @param int $storeId
     * @return string
     */
    public function getQuoteUrl($quoteId, $storeId = Store::DEFAULT_STORE_ID)
    {
        return $this->urlBuilder
            ->setScope($this->storeManager->getStore($storeId))
            ->getUrl('aw_ctq/quote/edit', ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve external quote url
     *
     * @param string $hash
     * @param int $storeId
     * @return string
     */
    public function getExternalQuoteUrl($hash, $storeId = Store::DEFAULT_STORE_ID)
    {
        return $this->urlBuilderFrontend
            ->setScope($this->storeManager->getStore($storeId))
            ->getUrl('aw_ctq/quote/external_edit', ['hash' => $hash]);
    }

    /**
     * Retrieve frontend quote url
     *
     * @param int $quoteId
     * @param int $storeId
     * @return string
     */
    public function getFrontendQuoteUrl($quoteId, $storeId = Store::DEFAULT_STORE_ID)
    {
        return $this->urlBuilderFrontend
            ->setScope($this->storeManager->getStore($storeId))
            ->getUrl('aw_ctq/quote/edit', ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve admin quote url
     *
     * @param int $quoteId
     * @return string
     */
    public function getAdminQuoteUrl($quoteId)
    {
        return $this->urlBuilderBackend->getUrl('aw_ctq/quote/edit', ['id' => $quoteId]);
    }

    /**
     * Retrieve downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilder->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve frontend downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getFrontendDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilderFrontend->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve frontend download external url
     *
     * @param string $attachmentFileName
     * @param string $hash
     * @param int $commentId
     * @return string
     */
    public function getFrontendDownloadExternalUrl($attachmentFileName, $hash, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'hash' => $hash,
            'comment_id' => $commentId
        ];

        return $this->urlBuilderFrontend->getUrl('aw_ctq/quote_external/download', $params);
    }

    /**
     * Retrieve admin downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @return string
     */
    public function getAdminDownloadUrl($attachmentFileName, $quoteId, $commentId)
    {
        $params = [
            'file' => $attachmentFileName,
            'quote_id' => $quoteId,
            'comment_id' => $commentId
        ];

        return $this->urlBuilderBackend->getUrl('aw_ctq/quote/download', $params);
    }

    /**
     * Retrieve add comment url
     *
     * @return string
     */
    public function getAddCommentUrl()
    {
        return $this->urlBuilder->getUrl('aw_ctq/quote/addComment');
    }

    /**
     * Retrieve add comment external url
     *
     * @return string
     */
    public function getAddCommentExternalUrl()
    {
        return $this->urlBuilder->getUrl('aw_ctq/quote_external/addComment');
    }
}
