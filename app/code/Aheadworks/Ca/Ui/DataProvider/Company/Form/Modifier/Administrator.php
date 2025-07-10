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

namespace Aheadworks\Ca\Ui\DataProvider\Company\Form\Modifier;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Administrator
 */
class Administrator implements ModifierInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var array
     */
    private $additionalFields;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CompanyUserManagementInterface $companyUserManagement
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $additionalFields
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        CompanyUserManagementInterface $companyUserManagement,
        AttributeRepositoryInterface $attributeRepository,
        array $additionalFields = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->companyUserManagement = $companyUserManagement;
        $this->additionalFields = $additionalFields;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $result = $data;
        if (!empty($data) && !isset($data['company'])) {
            $newData['company'] = $data;
            if (isset($data[CompanyInterface::ID]) && !empty($data[CompanyInterface::ID])) {
                $newData = array_merge(
                    $newData,
                    $this->getCustomerInformation($data[CompanyInterface::ID])
                );
            }
            $result = $newData;
        }

        return $result;
    }

    /**
     * Modify meta
     *
     * @param array $meta
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyMeta(array $meta)
    {
        foreach ($this->additionalFields as $additionalField) {
            $customerAttribute = $this->attributeRepository->get('customer', $additionalField['name']);
            if ($customerAttribute->getIsVisible()) {
                $meta['components']['awCaForm']['children']['administrator']['children'][$additionalField['name']]['visible'] = true;
                if ($customerAttribute->getIsRequired()) {
                    $meta['components']['awCaForm']['children']['administrator']['children']
                    [$additionalField['name']]['validation']['required-entry'] = true;
                }

            }
        }

        return $meta;
    }

    /**
     * Get customer information as array
     *
     * @param int $companyId
     * @return array
     */
    private function getCustomerInformation($companyId)
    {
        try {
            $rootCompanyUser = $this->companyUserManagement->getRootUserForCompany($companyId);
            $rootCompanyUserData = $this->dataObjectProcessor->buildOutputDataArray(
                $rootCompanyUser,
                CustomerInterface::class
            );
        } catch (NoSuchEntityException $exception) {
            $rootCompanyUserData = [];
        }

        return $rootCompanyUserData;
    }
}
