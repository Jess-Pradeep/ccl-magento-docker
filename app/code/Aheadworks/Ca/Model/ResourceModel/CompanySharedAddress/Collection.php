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

namespace Aheadworks\Ca\Model\ResourceModel\CompanySharedAddress;

use Aheadworks\Ca\Model\CompanySharedAddress;
use Aheadworks\Ca\Model\ResourceModel\AbstractCollection;
use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddress as CompanySharedAddressResource;
use Aheadworks\Ca\Model\CompanySharedAddressInterface;

class Collection extends AbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = CompanySharedAddressInterface::ID;

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CompanySharedAddress::class, CompanySharedAddressResource::class);
    }
}
