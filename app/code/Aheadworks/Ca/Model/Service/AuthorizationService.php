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
namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Authorization\Acl\ResourceMapper;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\AuthorizationInterface;
use Aheadworks\Ca\Model\Authorization\CustomProcessor\ProcessorInterface;

/**
 * Class AuthorizationService
 * @package Aheadworks\Ca\Model\Service
 */
class AuthorizationService implements AuthorizationManagementInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ResourceMapper
     */
    private $resourceMapper;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var ProcessorInterface
     */
    private $customProcessor;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param AuthorizationInterface $authorization
     * @param ResourceMapper $resourceMapper
     * @param UserContextInterface $userContext
     * @param ProcessorInterface $customProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        AuthorizationInterface $authorization,
        ResourceMapper $resourceMapper,
        UserContextInterface $userContext,
        ProcessorInterface $customProcessor,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->authorization = $authorization;
        $this->resourceMapper = $resourceMapper;
        $this->userContext = $userContext;
        $this->customProcessor = $customProcessor;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($path)
    {
        $resource = $this->resourceMapper->getResourceByPath($path);
        return $resource ? $this->isAllowedByResource($resource) : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByResource($resource)
    {
        $result = true;
        $currentUser = $this->companyUserManagement->getCurrentUser();
        $isRoot = false;
        $module = explode('::', (string)$resource)[0];
        if ($currentUser) {
            $isAllowedResource = $this->authorization->isAllowed($resource);
            $customProcessorResult = $this->customProcessor->isAllowed($resource);
            if ($currentUser->getExtensionAttributes() && $currentUser->getExtensionAttributes()->getAwCaCompanyUser()) {
                $isRoot = $currentUser->getExtensionAttributes()->getAwCaCompanyUser()->getIsRoot();
            }
            $result = ($isAllowedResource && $customProcessorResult) || $isRoot;
        } elseif (!$currentUser && $module == 'Aheadworks_Ca') {
            $result = false;
        }

        return $result;
    }
}
