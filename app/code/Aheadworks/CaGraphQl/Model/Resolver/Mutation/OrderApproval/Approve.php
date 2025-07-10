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
 * @package    CaGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\OrderApproval;

use Aheadworks\Ca\Api\OrderApprovalManagementInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\CaGraphQl\Model\Resolver\AuthorizedCompanyUserResolver;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Approve extends AuthorizedCompanyUserResolver implements ResolverInterface
{
    public const APPROVE_ACTION = 'approve';
    public const REJECT_ACTION = 'reject';

    /**
     * @param CompanyUserProvider $companyUserProvider
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderApprovalManagementInterface $orderApprovalManagement
     * @param string $action
     */
    public function __construct(
        CompanyUserProvider $companyUserProvider,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderApprovalManagementInterface $orderApprovalManagement,
        private readonly string $action = self::APPROVE_ACTION
    ) {
        parent::__construct($companyUserProvider);
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws Exception
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): bool {
        $this->ensureCompanyUserAuthorized($context);
        if (empty($args['order_id'])) {
            throw new GraphQlInputException(__('Required parameter "order_id" is missing'));
        }
        $order = $this->orderRepository->get($args['order_id']);
        if ($this->action === self::APPROVE_ACTION) {
            return $this->orderApprovalManagement->approveOrder($order);
        }

        if ($this->action === self::REJECT_ACTION) {
            return $this->orderApprovalManagement->rejectOrder($order);
        }

        return false;
    }
}
