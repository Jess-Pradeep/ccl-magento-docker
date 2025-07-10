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
namespace Aheadworks\Ctq\Model\Data\Command\Quote;

use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Model\Attachment\File\Downloader as FileDownloader;

/**
 * Class AddComment
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote
 */
class Download implements CommandInterface
{
    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @param CommentManagementInterface $commentManagement
     * @param FileDownloader $fileDownloader
     */
    public function __construct(
        CommentManagementInterface $commentManagement,
        FileDownloader $fileDownloader
    ) {
        $this->commentManagement = $commentManagement;
        $this->fileDownloader = $fileDownloader;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['file'])) {
            throw new \InvalidArgumentException('file argument is required');
        }
        if (!isset($data['comment_id'])) {
            throw new \InvalidArgumentException('comment_id argument is required');
        }
        if (!isset($data['quote_id'])) {
            throw new \InvalidArgumentException('quote_id argument is required');
        }

        $attachment = $this->commentManagement->getAttachment(
            $data['file'],
            $data['comment_id'],
            $data['quote_id']
        );

        return $this->fileDownloader->download($attachment);
    }
}
