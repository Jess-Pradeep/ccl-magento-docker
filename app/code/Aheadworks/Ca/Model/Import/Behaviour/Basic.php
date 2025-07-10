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

namespace Aheadworks\Ca\Model\Import\Behaviour;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

/**
 * Import behavior source model used for defining the behaviour during the import
 */
class Basic extends AbstractBehavior
{
    /**
     * Get array of possible values
     *
     * @abstract
     * @return array
     */
    public function toArray(): array
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update')
        ];
    }

    /**
     * Get current behaviour group code
     *
     * @abstract
     * @return string
     */
    public function getCode(): string
    {
        return 'aw_ca_company_create';
    }

    /**
     * Get array of notes for possible values
     *
     * @param string $entityCode
     * @return array
     */
    public function getNotes($entityCode): array
    {
        $messages = [
            'aw_ca_company' => [
                Import::BEHAVIOR_APPEND => __(
                    'New company data is added to the existing companies (mapping by company ID) ' .
                    'or creates completely new companies.'
                )
            ]
        ];

        return $messages[$entityCode] ?? [];
    }
}
