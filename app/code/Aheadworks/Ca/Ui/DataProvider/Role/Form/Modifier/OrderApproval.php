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
namespace Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier;

use Aheadworks\Ca\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\ViewModel\Role\Role as RoleViewModel;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;

/**
 * Class OrderApproval
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class OrderApproval implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleViewModel
     */
    private $roleViewModel;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleViewModel $roleViewModel
     * @param CompanyUserProvider $companyUserProvider
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleViewModel $roleViewModel,
        CompanyUserProvider $companyUserProvider,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleViewModel = $roleViewModel;
        $this->companyUserProvider = $companyUserProvider;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (isset($data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT])) {
            $data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT] = $this->roleViewModel->getRoundAmount(
                $data[RoleInterface::ORDER_BASE_AMOUNT_LIMIT]
            );
        }

        return $data;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta)
    {
        $orderLimitFieldPath = $this->arrayManager->findPath('order_base_amount_limit', $meta);
        if ($orderLimitFieldPath) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();

            $orderLimitFieldConfig['visible'] = $this->companyUserProvider->isCurrentCompanyUserRoot()
                && $this->config->isOrderApprovalEnabled($websiteId);
            $meta = $this->arrayManager->merge($orderLimitFieldPath, $meta, $orderLimitFieldConfig);
        }

        return $meta;
    }
}
