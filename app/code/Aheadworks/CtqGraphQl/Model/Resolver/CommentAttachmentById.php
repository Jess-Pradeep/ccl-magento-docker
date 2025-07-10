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

namespace Aheadworks\CtqGraphQl\Model\Resolver;

use Aheadworks\Ctq\Api\CommentManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentAttachmentInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Reflection\DataObjectProcessor;

class CommentAttachmentById extends AbstractResolver
{
    /**
     * @param QuoteRepositoryInterface $repository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CommentManagementInterface $commentManagement
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly CommentManagementInterface $commentManagement
    ) {
    }

    /**
     * Perform resolve method after validate customer authorization
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $maskedQuoteId = $args['quoteHash'];
        $quote = $this->repository->getByHash($maskedQuoteId);
        $attachment = $this->commentManagement->getAttachment($args['fileName'], $args['commentId'], $quote->getId());

        return $this->dataObjectProcessor->buildOutputDataArray($attachment, CommentAttachmentInterface::class);
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
        if (isset($args['fileName']) && empty($args['fileName'])) {
            throw new GraphQlInputException(__('Specify the "fileName" value.'));
        }
        if (isset($args['commentId']) && $args['commentId'] < 1) {
            throw new GraphQlInputException(__('Specify the "commentId" value.'));
        }
        if (isset($args['quoteHash']) && empty($args['quoteHash'])) {
            throw new GraphQlInputException(__('Specify the "quoteHash" value.'));
        }

        return true;
    }
}
