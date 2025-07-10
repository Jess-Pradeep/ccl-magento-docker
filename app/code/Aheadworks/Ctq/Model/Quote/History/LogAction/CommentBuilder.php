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
namespace Aheadworks\Ctq\Model\Quote\History\LogAction;

use Aheadworks\Ctq\Model\Comment\ToHistory as CommentToHistory;

/**
 * Class CommentBuilder
 * @package Aheadworks\Ctq\Model\Quote\History\LogAction
 */
class CommentBuilder implements BuilderInterface
{
    /**
     * @var CommentToHistory
     */
    private $commentToHistory;

    /**
     * @param CommentToHistory $commentToHistory
     */
    public function __construct(
        CommentToHistory $commentToHistory
    ) {
        $this->commentToHistory = $commentToHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($quote)
    {
        $historyActions = [];
        if ($comment = $quote->getComment()) {
            $commentHistory = $this->commentToHistory->convert($comment);

            $historyActions = $commentHistory->getActions();
        }

        return $historyActions;
    }
}
