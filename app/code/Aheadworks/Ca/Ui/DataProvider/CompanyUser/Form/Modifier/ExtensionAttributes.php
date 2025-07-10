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

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Repository;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ExtensionAttributes
 */
class ExtensionAttributes implements ModifierInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Repository
     */
    private $companyUserRepository;

    /**
     * @var array
     */
    private $additionalFields;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Repository $companyUserRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $additionalFields
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        Repository $companyUserRepository,
        AttributeRepositoryInterface $attributeRepository,
        ArrayManager $arrayManager,
        array $additionalFields = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->companyUserRepository = $companyUserRepository;
        $this->additionalFields = $additionalFields;
        $this->attributeRepository = $attributeRepository;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $entityFieldName = 'entity_id';
        if (!isset($data['extension_attributes'])
            && isset($data[$entityFieldName])
            && !empty($data[$entityFieldName])
        ) {
            $attributes = [
                'extension_attributes' => [
                    'aw_ca_company_user' => $this->loadCompanyUserAttributes($data[$entityFieldName])
                ]
            ];
            $data = array_merge($data, $attributes);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        foreach ($this->additionalFields as $additionalField) {
            $customerAttribute = $this->attributeRepository->get('customer', $additionalField['name']);
            if ($customerAttribute->getIsVisible()) {
                $fieldsetPath = $this->arrayManager->findPath($additionalField, $meta);
                $meta = $this->arrayManager->set($fieldsetPath.'/visible', $meta , true);
                if ($customerAttribute->getIsRequired()) {
                    $meta = $this->arrayManager->set($fieldsetPath.'/validation/required-entry', $meta , true);
                }
            }
        }

        return $meta;
    }

    /**
     * Get company user extension attributes
     *
     * @param int $companyUserId
     * @return array
     */
    private function loadCompanyUserAttributes($companyUserId)
    {
        try {
            $companyUser = $this->companyUserRepository->get($companyUserId);
            $companyUserData = $this->dataObjectProcessor->buildOutputDataArray(
                $companyUser,
                CompanyUserInterface::class
            );
        } catch (NoSuchEntityException $exception) {
            $companyUserData = [];
        } catch (LocalizedException $exception) {
            $companyUserData = [];
        }

        return $companyUserData;
    }
}
