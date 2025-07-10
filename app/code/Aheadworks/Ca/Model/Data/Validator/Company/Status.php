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

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Source\Company\Status as StatusSource;

/**
 * Class Status
 *
 * @package Aheadworks\Ca\Model\Data\Validator\Company
 */
class Status extends AbstractValidator
{
    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * Status constructor.
     * @param StatusSource $statusSource
     */
    public function __construct(
        StatusSource $statusSource
    ) {
        $this->statusSource = $statusSource;
    }

    /**
     * Returns true if status is valid
     *
     * @param CompanyInterface $company
     * @return bool
     */
    public function isValid($company)
    {
        if (!$this->statusSource->isValidStatus($company->getStatus())) {
            $this->_addMessages([__('Status is not correct for Company.')]);
        }

        return empty($this->getMessages());
    }
}
