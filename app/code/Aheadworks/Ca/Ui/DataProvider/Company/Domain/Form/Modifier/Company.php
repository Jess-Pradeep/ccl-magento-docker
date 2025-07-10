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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Domain\Form\Modifier;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class Company
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Domain\Form\Modifier
 */
class Company implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CompanyUserManagementInterface
     */
    private $userManagement;

    /**
     * @param ArrayManager $arrayManager
     * @param CompanyUserManagementInterface $userManagement
     */
    public function __construct(
        ArrayManager $arrayManager,
        CompanyUserManagementInterface $userManagement
    ) {
        $this->arrayManager = $arrayManager;
        $this->userManagement = $userManagement;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $user = $this->userManagement->getCurrentUser();
        if ($user && $user->getExtensionAttributes()->getAwCaCompanyUser()) {
            /** @var CompanyUserInterface $companyUser */
            $companyUser = $user->getExtensionAttributes()->getAwCaCompanyUser();
            $providerPath = $this->arrayManager->findPath('awCaDomainProvider', $meta);
            if ($providerPath) {
                $config['data']['company'] = [
                    'id' => $companyUser->getCompanyId()
                ];
                $meta = $this->arrayManager->merge($providerPath, $meta, $config);
            }
        }

        return $meta;
    }
}
