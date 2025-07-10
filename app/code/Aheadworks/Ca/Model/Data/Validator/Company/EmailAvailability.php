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
namespace Aheadworks\Ca\Model\Data\Validator\Company;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Customer\Checker\EmailAvailability\Checker;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;

/**
 * Class EmailAvailability
 *
 * @package Aheadworks\Ca\Model\Data\Validator\Company
 */
class EmailAvailability extends AbstractValidator
{
    /**
     * @var Checker
     */
    private $emailChecker;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @param Checker $emailChecker
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        Checker $emailChecker,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->emailChecker = $emailChecker;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Returns true if company email is already used
     *
     * @param CompanyInterface $company
     * @return bool
     * @throws LocalizedException
     */
    public function isValid($company)
    {
        $emailToCheck = $company->getEmail();
        if ($company->getId()) {
            $oldCompany = $this->companyRepository->get($company->getId(), true);
            if ($oldCompany->getEmail() == $emailToCheck) {
                return true;
            }
        }
        if (!$this->emailChecker->isEmailAvailableForCompany($emailToCheck)) {
            $this->_addMessages([__('The company email is already registered.')]);
        }

        return empty($this->getMessages());
    }
}
