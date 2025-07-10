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

use Aheadworks\Ctq\Model\Config as QuoteConfig;
use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Config extends AbstractResolver
{
    /**
     * @param QuoteConfig $config
     */
    public function __construct(
        private readonly QuoteConfig $config
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
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->validateArgs($args);
        $websiteId = $args['websiteId'] ?? null;

        return [
            'is_quote_list_enabled' => $this->config->isQuoteListEnabled($websiteId),
            'minimum_quote_subtotal' => $this->config->getMinimumQuoteSubtotal($websiteId)
        ];
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
        if (isset($args['websiteId']) && $args['websiteId'] < 1) {
            throw new GraphQlInputException(__('Specify the "websiteId" value.'));
        }

        return true;
    }
}
