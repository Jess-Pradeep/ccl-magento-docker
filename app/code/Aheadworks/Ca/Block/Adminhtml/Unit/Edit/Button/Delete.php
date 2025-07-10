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

namespace Aheadworks\Ca\Block\Adminhtml\Unit\Edit\Button;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;

class Delete extends AbstractButton
{
    /**
     * Delete Construct
     *
     * @param Context $context
     * @param UnitRepositoryInterface $unitRepository
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        Context $context,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly CompanyRepositoryInterface $companyRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        $unit = $this->getUnit();
        $company = $this->getCompany();
        $rootUnit = $this->unitRepository->getCompanyRootUnit($company->getId());
        if ($unit && $unit->getId() != $rootUnit->getId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . $this->getConfirmMessage()
                    . '\', \'' . $this->getUrl(
                        '*/*/delete',
                        [
                            'company_id' => $company->getId(),
                            'id' => $unit->getId()
                        ]
                    ) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get unit
     *
     * @return UnitInterface|null
     */
    private function getUnit()
    {
        $unit = null;
        $unitId = $this->context->getRequest()->getParam('id');
        if ($unitId) {
            try {
                $unit = $this->unitRepository->get($unitId);
            } catch (NoSuchEntityException $exception) {
                $unit = null;
            }
        }

        return $unit;
    }

    /**
     * Get company
     *
     * @return CompanyInterface|null
     */
    private function getCompany()
    {
        $company = null;
        $companyId = $this->context->getRequest()->getParam('company_id');
        if ($companyId) {
            try {
                $company = $this->companyRepository->get($companyId);
            } catch (NoSuchEntityException $exception) {
                $company = null;
            }
        }

        return $company;
    }

    /**
     * Get confirm message
     *
     * @return Phrase
     */
    private function getConfirmMessage()
    {
        return __(
            'Do you want to delete this unit? '
                . 'Related sub-units will be deleted.'
        );
    }
}
