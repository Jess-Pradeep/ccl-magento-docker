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
namespace Aheadworks\Ca\Block\Role\Edit\Tab\AdvancedPermissions;

use Magento\Framework\Data\Form\Element\Fieldset;
use Aheadworks\SalesRepresentative\Block\Role\Edit\Tab\AdvancedPermissions;

/**
 * Class CompanyAccess
 * @package Aheadworks\Ca\Block\Role\Edit\Tab\AdvancedPermissions
 */
class CompanyAccess
{
    /**
     * @var string
     */
    const COMPANY_ACCESS = 'Aheadworks_Ca::companies';

    /**
     * Add company access field to fieldset
     *
     * @param Fieldset $fieldset
     * @param int|null $value
     * @return void
     */
    public function addToFieldset($fieldset, $value)
    {
        $fieldset->addField(
            self::COMPANY_ACCESS,
            'select',
            [
                'name' => AdvancedPermissions::FORM_ELEMENT_PREFIX . '[' . self::COMPANY_ACCESS . ']',
                'label' => __('Allow Access to Companies'),
                'title' => __('Allow Access to Companies'),
                'id' => self::COMPANY_ACCESS,
                'value' => $value,
                'options' => [
                    '1' => __('YES'),
                    '0' => __('NO')
                ],
                'note' => __(
                    'Set to Yes and users with this user role will see only the data which they are assigned to'
                )
            ]
        );
    }
}
