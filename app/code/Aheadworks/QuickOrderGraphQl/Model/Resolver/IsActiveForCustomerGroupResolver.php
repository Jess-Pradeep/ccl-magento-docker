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
 * @package    QuickOrderGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Aheadworks\QuickOrder\Api\CustomerManagementInterface;

/**
 * Class IsActiveForCustomerGroupResolver
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver
 */
class IsActiveForCustomerGroupResolver implements ResolverInterface
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerService;

    /**
     * @param CustomerManagementInterface $customerService
     */
    public function __construct(
        CustomerManagementInterface $customerService
    ) {
        $this->customerService = $customerService;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (empty($args['customerGroupId'])) {
            throw new GraphQlInputException(__('Required parameter "customerGroupId" is missing'));
        }

        $websiteId = (int)$context->getExtensionAttributes()->getStore()->getWebsiteId();
        return $this->customerService->isActiveForCustomerGroup($args['customerGroupId'], $websiteId);
    }
}
