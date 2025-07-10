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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\Page;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Model\Source\Owner;

/**
 * Class AddComment
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class AddComment extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @var CommandInterface
     */
    private $addCommentCommand;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     * @param CommandInterface $addCommentCommand
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        CommandInterface $addCommentCommand
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->addCommentCommand = $addCommentCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = ['error' => true, 'message' => __('Invalid response data.')];

        if ($data = $this->getRequest()->getPostValue()) {
            $quoteId = $this->getRequest()->getParam(CommentInterface::QUOTE_ID);
            if ($quoteId) {
                try {
                    $data[CommentInterface::OWNER_TYPE] = Owner::SELLER;
                    $this->addCommentCommand->execute($data);

                    /** @var Page $response */
                    $response = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                    $layout = $response->addHandle('aw_ctq_quote_edit_comments_and_history_layout')->getLayout();
                    $commentHtml = $layout->getBlock('aw_ctq.customer.quote.comment.list')->toHtml();

                    $result = ['error' => false, 'content' => $commentHtml];
                } catch (LocalizedException $e) {
                    $result = ['error' => true, 'message' => __($e->getMessage())];
                } catch (\Exception $e) {
                    $result = ['error' => true, 'message' => __($e->getMessage())];
                }
            }
        }

        return $resultJson->setData($result);
    }
}
