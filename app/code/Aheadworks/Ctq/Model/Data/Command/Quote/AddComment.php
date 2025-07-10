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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;

/**
 * Class AddComment
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote
 */
class AddComment implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CommentInterfaceFactory $commentFactory
     * @param CommentManagementInterface $commentManagement
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CommentInterfaceFactory $commentFactory,
        CommentManagementInterface $commentManagement,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->commentFactory = $commentFactory;
        $this->commentManagement = $commentManagement;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        /** @var CommentInterface $commentObject */
        $commentObject = $this->commentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentObject,
            $data,
            CommentInterface::class
        );

        if (!$commentObject->getComment() && !$commentObject->getAttachments()) {
            throw new LocalizedException(__('Please, add comment or(and) attach files.'));
        }

        $quote = $this->quoteRepository->get($commentObject->getQuoteId());
        if (isset($data[CommentInterface::OWNER_TYPE])
            && $data[CommentInterface::OWNER_TYPE] == Owner::SELLER
        ) {
            $commentObject
                ->setOwnerId($quote->getSellerId())
                ->setOwnerType(Owner::SELLER);
        } else {
            $commentObject->setOwnerType(Owner::BUYER);
            if (!$quote->getCustomerId()) {
                $commentObject->setOwnerName($quote->getCustomerLastName() . '' . $quote->getCustomerLastName());
            } else {
                $commentObject->setOwnerId($quote->getCustomerId());
            }
        }

        return $this->commentManagement->addComment($commentObject);
    }
}
