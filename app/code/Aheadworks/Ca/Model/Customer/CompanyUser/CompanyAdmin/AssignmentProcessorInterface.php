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

namespace Aheadworks\Ca\Model\Customer\CompanyUser\CompanyAdmin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

interface AssignmentProcessorInterface
{
    /**
     * Move any data from old company admin to new company admin
     *
     * @param CustomerInterface $oldCompanyAdmin
     * @param CustomerInterface $newCompanyAdmin
     * @return bool
     * @throws LocalizedException
     */
    public function process(CustomerInterface $oldCompanyAdmin, CustomerInterface $newCompanyAdmin): bool;
}
