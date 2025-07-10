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
namespace Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Source\Role\Role as RoleSource;
use Magento\Framework\App\RequestInterface;

/**
 * Class Role
 * @package Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier
 */
class Role implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleSource
     */
    private $roleSource;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleSource $roleSource
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleSource $roleSource,
        CompanyUserManagementInterface $companyUserManagement,
        RequestInterface $request
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleSource = $roleSource;
        $this->companyUserManagement = $companyUserManagement;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $rolePath = $this->arrayManager->findPath('role', $meta);
        $roleOptions = $this->createOptionArray();

        $isDefaultRoleId = '';
        foreach ($roleOptions as $option) {
            if ($option['is_default']) {
                $isDefaultRoleId = $option['value'];
            }
        }

        $role = [
            'options' => $roleOptions,
            'default' => $isDefaultRoleId
        ];
        $meta = $this->arrayManager->merge($rolePath, $meta, $role);

        if (is_null($rolePath)) {
            $meta = $this->arrayManager->merge('general/children/company_role_id/arguments/data/config', $meta, $role);
        }

        return $meta;
    }

    /**
     * Retrieve roles as option array
     *
     * @return array
     */
    public function createOptionArray()
    {
        if ($user = $this->companyUserManagement->getCurrentUser()) {
            $companyId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            $this->roleSource->getSearchCriteriaBuilder()->addFilter(RoleInterface::COMPANY_ID, $companyId);
        } elseif ($companyId = $this->request->getParam('company_id')) {
            $this->roleSource->getSearchCriteriaBuilder()->addFilter(RoleInterface::COMPANY_ID, $companyId);
        }

        return $this->roleSource->toOptionArray();
    }
}
