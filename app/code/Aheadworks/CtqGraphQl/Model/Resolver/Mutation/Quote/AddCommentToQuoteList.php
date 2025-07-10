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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\Resolver\Mutation\Quote;

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Api\CommentRepositoryInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Owner;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;

class AddCommentToQuoteList extends AbstractResolver
{
    /**
     * @param QuoteRepositoryInterface $repository
     * @param DataObjectHelper $dataObjectHelper
     * @param CommentInterfaceFactory $commentFactory
     * @param CommentManagementInterface $commentManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly CommentInterfaceFactory $commentFactory,
        private readonly CommentManagementInterface $commentManagement,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly CommentRepositoryInterface $commentRepository
    ) {
    }

    /**
     * Perform resolve method
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $maskedQuoteId = $args['input']['quote_hash'];

        $quote = $this->repository->getByHash($maskedQuoteId);
        $data = $this->prepareData($args, $quote);

        /** @var CommentInterface $commentObject */
        $commentObject = $this->commentFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentObject,
            $data,
            CommentInterface::class
        );

        $commentObject->setOwnerType(Owner::BUYER);
        if (!$quote->getCustomerId()) {
            $commentObject->setOwnerName($quote->getCustomerLastName() . ' ' . $quote->getCustomerFirstName());
        } else {
            $commentObject->setOwnerId($quote->getCustomerId());
        }

        $this->commentManagement->addComment($commentObject);
        $comment = $this->commentRepository->get($commentObject->getId());

        return  $this->dataObjectProcessor->buildOutputDataArray(
            $comment,
            CommentInterface::class
        );
    }

    /**
     * Validate requested arguments
     *
     * @param array $args
     * @return bool|GraphQlInputException
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args): bool|GraphQlInputException
    {
        if (isset($args['input']['quote_hash']) && empty($args['input']['quote_hash'])) {
            throw new GraphQlInputException(__('Specify the "quote_id" value.'));
        }
        if (isset($args['input']['comment']) && empty($args['input']['comment'])) {
            throw new GraphQlInputException(__('Specify the "status" value.'));
        }

        return true;
    }

    /**
     * Prepare data
     *
     * @param array $args
     * @param QuoteInterface $quote
     * @return array[]
     */
    private function prepareData(array $args, QuoteInterface $quote): array
    {
        $data = [
            'quote_id' => $quote->getId(),
            'comment' => $args['input']['comment'],
            'attachments' => $args['input']['attachments'] ?? ''
        ];

        return $data;
    }
}
