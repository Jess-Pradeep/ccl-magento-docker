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

use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ChangeStatus extends AbstractResolver
{
    /**
     * @var array
     */
    private array $status = [
        'pending' => Status::PENDING_SELLER_REVIEW,
        'decline' => Status::DECLINED_BY_BUYER
    ];

    /**
     * @param QuoteRepositoryInterface $repository
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly BuyerQuoteManagementInterface $buyerQuoteManagement
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
     * @throws NoSuchEntityException
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
        $status = $this->status[strtolower($args['input']['status'])];
        if ($quote->getStatus() !== $status && ($quote->getStatus() === Status::PENDING_BUYER_REVIEW
                || $quote->getStatus() === Status::ACCEPTED)) {
            try {
                $result = $this->buyerQuoteManagement->changeStatus($quote->getId(), $status);
            } catch (LocalizedException $e) {
                throw new GraphQlInputException(__($e->getMessage()));
            }
        } else {
            throw new GraphQlInputException(__('Cannot change quote status'));
        }

        return $result;
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
        if (isset($args['input']['status']) && empty($args['input']['status'])) {
            throw new GraphQlInputException(__('Specify the "status" value.'));
        }
        if (!isset($this->status[strtolower($args['input']['status'])])) {
            throw new GraphQlInputException(__('Specify the "status" value must be "pending" or "decline"'));
        }

        return true;
    }
}
