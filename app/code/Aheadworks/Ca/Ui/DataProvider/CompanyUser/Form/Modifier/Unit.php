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
declare(strict_types=1);

namespace Aheadworks\Ca\Ui\DataProvider\CompanyUser\Form\Modifier;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Model\Source\Unit\Unit as UnitSource;
use Magento\Framework\App\RequestInterface;

class Unit implements ModifierInterface
{
    /**
     * Unit Construct
     *
     * @param ArrayManager $arrayManager
     * @param UnitSource $unitSource
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly ArrayManager $arrayManager,
        private readonly UnitSource $unitSource,
        private readonly CompanyUserManagementInterface $companyUserManagement,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Modify Data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * Modify Meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        $unitPath = $this->arrayManager->findPath('unit', $meta);
        $unitOptions = $this->createOptionArray();
        $unit = [
            'options' => $unitOptions
        ];
        $meta = $this->arrayManager->merge($unitPath, $meta, $unit);

        if ($unitPath === null) {
            $meta = $this->arrayManager->merge(
                'general/children/company_unit_id/arguments/data/config',
                $meta,
                $unit
            );
        }

        return $meta;
    }

    /**
     * Retrieve units as option array
     *
     * @return array
     */
    public function createOptionArray(): array
    {
        if ($user = $this->companyUserManagement->getCurrentUser()) {
            $companyId = $user->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();
            $this->unitSource->getSearchCriteriaBuilder()->addFilter(UnitInterface::COMPANY_ID, $companyId);
        } elseif ($companyId = $this->request->getParam('company_id')) {
            $this->unitSource->getSearchCriteriaBuilder()->addFilter(UnitInterface::COMPANY_ID, $companyId);
        }

        return $this->unitSource->toOptionArray();
    }
}
