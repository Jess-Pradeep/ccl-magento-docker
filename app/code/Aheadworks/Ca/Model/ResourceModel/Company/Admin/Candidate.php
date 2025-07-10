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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Admin;

use Aheadworks\Ca\Api\Data\CompanyAdminCandidateInterface;
use Aheadworks\Ca\Model\ResourceModel\AbstractResourceModel;

class Candidate extends AbstractResourceModel
{
    public const MAIN_TABLE_NAME = 'aw_ca_company_admin_candidate';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE_NAME, CompanyAdminCandidateInterface::ID);
    }
}
