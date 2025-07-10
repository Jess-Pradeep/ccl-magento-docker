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
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterfaceFactory;
use Aheadworks\Ctq\Model\Service\BuyerPermissionService;
use Aheadworks\CtqGraphQl\Model\Cart\GetCartForUser;
use Aheadworks\CtqGraphQl\Model\ObjectConverter;
use Aheadworks\CtqGraphQl\Model\Resolver\AbstractResolver;
use GraphQL\Error\ClientAware;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RequestQuoteList extends AbstractResolver
{
    /**
     * @var array
     */
    private array $requiredField = [
        'cart_hash',
        'quote_name',
        'comment'
    ];

    /**
     * @param RequestQuoteForCustomer $requestQuoteForCustomer
     * @param RequestQuoteForGuest $requestQuoteForGuest
     * @param ObjectConverter $objectConverter
     * @param BuyerPermissionService $buyerPermissionService
     * @param GetCartForUser $getCartForUser
     * @param RequestQuoteInputInterfaceFactory $requestInputFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        private readonly RequestQuoteForCustomer $requestQuoteForCustomer,
        private readonly RequestQuoteForGuest $requestQuoteForGuest,
        private readonly ObjectConverter $objectConverter,
        private readonly BuyerPermissionService $buyerPermissionService,
        private readonly GetCartForUser $getCartForUser,
        private readonly RequestQuoteInputInterfaceFactory $requestInputFactory,
        private readonly DataObjectHelper $dataObjectHelper
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
     * @return array
     * @throws AggregateExceptionInterface
     * @throws ClientAware
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->validateArgs($args);

        $customerId = $context->getUserId();
        $maskedCartId = $args['input']['cart_hash'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
        if (!$this->buyerPermissionService->canRequestQuoteList($cart->getId())) {
            throw new GraphQlInputException(__('Cannot request quote list'));
        }
        $data = $this->prepareData($args, $context);

        /** @var RequestQuoteInputInterface $requestInput */
        $requestInput = $this->requestInputFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $requestInput,
            $data,
            RequestQuoteInputInterface::class
        );

        $quote = (0 === $customerId || null === $customerId)
            ? $this->requestQuoteForGuest->execute((int)$cart->getId(), $requestInput)
            : $this->requestQuoteForCustomer->execute((int)$cart->getId(), $requestInput);

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
        foreach ($this->requiredField as $value) {
            if (empty($args['input'][$value])) {
                throw new GraphQlInputException(__('Required parameter ' . $value . ' is missing or empty'));
            }
        }

        return true;
    }

    /**
     * Prepare data
     *
     * @param array $args
     * @param ContextInterface $context
     * @return array
     */
    private function prepareData(array $args, ContextInterface $context): array
    {
        $data = [
            'aw_ctq_is_quote_list' => 1,
            'is_guest_quote' => !$context->getUserId(),
            'comment' => [
                'comment' => $args['input']['comment'],
                'attachments' => $args['input']['attachments'] ?? ''
            ],
            'quote_name' => $args['input']['quote_name'],
            'customer_email' => '',
            'customer_first_name' => '',
            'customer_last_name' => '',
        ];

        return $data;
    }
}
