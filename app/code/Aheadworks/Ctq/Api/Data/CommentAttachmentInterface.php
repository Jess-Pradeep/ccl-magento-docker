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
namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface CommentAttachmentInterface
 * @api
 */
interface CommentAttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ATTACHMENT_ID = 'attachment_id';
    const COMMENT_ID = 'comment_id';
    const NAME = 'name';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get attachment id
     *
     * @return int
     */
    public function getAttachmentId();

    /**
     * Set attachment id
     *
     * @param int $attachmentId
     * @return $this
     */
    public function setAttachmentId($attachmentId);

    /**
     * Get comment id
     *
     * @return int
     */
    public function getCommentId();

    /**
     * Set comment id
     *
     * @param int $commentId
     * @return $this
     */
    public function setCommentId($commentId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
