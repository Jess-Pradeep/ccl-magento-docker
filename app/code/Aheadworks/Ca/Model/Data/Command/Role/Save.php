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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Data\Command\Role;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\Data\RoleInterfaceFactory;
use Aheadworks\Ca\Api\RoleManagementInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Ca\Model\Data\Command\Role
 */
class Save implements CommandInterface
{
    const CURRENT_COMPANY_ID = 'current_company_id';

    /**
     * @var RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param RoleInterfaceFactory $roleFactory
     * @param RoleManagementInterface $roleManagement
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        RoleInterfaceFactory $roleFactory,
        RoleManagementInterface $roleManagement,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->roleFactory = $roleFactory;
        $this->roleManagement = $roleManagement;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[self::CURRENT_COMPANY_ID])) {
            throw new \InvalidArgumentException(self::CURRENT_COMPANY_ID . ' argument is required');
        }

        if (isset($data['permissions'])) {
            $postedResources = $data['permissions'];
            unset($data['permissions']);
        } else {
            $postedResources = [];
        }

        if (isset($data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT])) {
            $orderAmount = $data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT];
            if (empty($orderAmount) && !is_numeric($orderAmount)) {
                $data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT] = null;
            }
        }

        /** @var RoleInterface $roleObject */
        $roleObject = $this->roleFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $roleObject,
            $data,
            RoleInterface::class
        );

        $roleObject->setCompanyId($data[self::CURRENT_COMPANY_ID]);
        return $this->roleManagement->saveRole($roleObject, $postedResources);
    }
}
