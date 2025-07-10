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

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Service\BuyerQuoteService;
use Aheadworks\CtqGraphQl\Model\ObjectConverter;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CopyQuote extends AbstractResolver
{
    /**
     * @param QuoteRepositoryInterface $repository
     * @param BuyerQuoteService $buyerQuoteService
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        private readonly QuoteRepositoryInterface $repository,
        private readonly BuyerQuoteService $buyerQuoteService,
        private readonly ObjectConverter $objectConverter
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
        try {
            $quote = $this->buyerQuoteService->copyQuote($quote);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $this->objectConverter->convertToArray($quote, QuoteInterface::class);
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

        return true;
    }
}
