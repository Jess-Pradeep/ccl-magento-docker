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
 * @package    RequisitionListsGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionListsGraphQl\Model\Resolver\Argument\Filter;

use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

class ReqListEntityAttributes implements FieldEntityAttributesInterface
{
    /**
     * @param ConfigInterface $config
     * @param null|array $additionalFields
     * @param string $entityElementName
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly string $entityElementName,
        private readonly ?array $additionalFields = []
    ) {
    }

    /**
     * Get the attributes for an entity
     *
     * @return array
     */
    public function getEntityAttributes(): array
    {
        $entityTypeSchema = $this->config->getConfigElement($this->entityElementName);
        if (!$entityTypeSchema) {
            throw new \LogicException((string)__('%1 type not defined in schema.', $this->entityElementName));
        }

        $fields = [];
        foreach ($entityTypeSchema->getFields() as $field) {
            $fields[$field->getName()] = [
                'type' => 'String',
                'fieldName' => $field->getName(),
            ];
        }

        foreach ($this->additionalFields as $additionalField) {
            $fields[$additionalField] = [
                'type' => 'String',
                'fieldName' => $additionalField,
            ];
        }

        return $fields;
    }
}
