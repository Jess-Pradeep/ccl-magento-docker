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

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\CompanyInterface;

/**
 * Class CompanyResource
 */
class Company extends AbstractResourceModel
{
    public const MAIN_TABLE_NAME = 'aw_ca_company';

    public const COMPANY_PAYMENTS_TABLE_NAME = 'aw_ca_company_payments';
    public const COMPANY_SHIPPING_METHODS_TABLE_NAME = 'aw_ca_company_shipping_methods';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE_NAME, CompanyInterface::ID);
    }
}
