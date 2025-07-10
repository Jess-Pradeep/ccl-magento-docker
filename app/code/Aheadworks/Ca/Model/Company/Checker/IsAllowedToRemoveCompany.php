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
namespace Aheadworks\Ca\Model\Company\Checker;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Source\Company\Status as CompanyStatus;

/**
 * Class IsAllowedToRemoveCompany
 *
 * @package Aheadworks\Ca\Model\Company\Checker
 */
class IsAllowedToRemoveCompany
{
    /**
     * Check whether company can be removed
     *
     * @param CompanyInterface $company
     * @return bool
     */
    public function check($company)
    {
        return $company->getStatus() == CompanyStatus::DECLINED;
    }
}
