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

namespace Aheadworks\Ca\Model\ResourceModel\Company\User;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * @var string
     */
    protected $_idFieldName = CompanyUserInterface::CUSTOMER_ID;

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CompanyUser::class, CompanyUserResourceModel::class);
    }
}
