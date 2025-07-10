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
 * @package    CaGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CaGraphQl\Model\Resolver\Argument\Filter;

use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Type;

class EntityAttributesForAst implements FieldEntityAttributesInterface
{
    /**
     * @var array
     */
    private array $additionalAttributes = [];

    /**
     * @param ConfigInterface $config
     * @param string|null $entityElementName
     * @param array $additionalAttributes
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ?string $entityElementName = null,
        array $additionalAttributes = []
    ) {
        $this->additionalAttributes = array_merge($this->additionalAttributes, $additionalAttributes);
    }

    /**
     * Get the attributes for an entity
     *
     * @return array
     */
    public function getEntityAttributes() : array
    {
        $entityTypeSchema = $this->config->getConfigElement($this->entityElementName);
        if (!$entityTypeSchema instanceof Type) {
            throw new \LogicException(sprintf('%s type not defined in schema.', $this->entityElementName));
        }

        $fields = [];
        foreach ($entityTypeSchema->getFields() as $field) {
            $fields[$field->getName()] = 'String';
        }

        foreach ($this->additionalAttributes as $attribute) {
            $fields[$attribute] = 'String';
        }

        return $fields;
    }
}
