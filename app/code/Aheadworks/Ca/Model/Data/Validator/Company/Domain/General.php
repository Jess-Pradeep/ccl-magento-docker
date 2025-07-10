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

namespace Aheadworks\Ca\Model\Data\Validator\Company\Domain;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Company\Domain\DisallowedDomainConfig;

/**
 * General validator
 */
class General extends AbstractValidator
{
    /**
     * @var DisallowedDomainConfig
     */
    private $domainConfig;

    /**
     * @param DisallowedDomainConfig $domainConfig
     */
    public function __construct(
        DisallowedDomainConfig $domainConfig
    ) {
        $this->domainConfig = $domainConfig;
    }

    /**
     * Returns true if domain is valid
     *
     * @param CompanyDomainInterface $value
     * @return bool
     */
    public function isValid($value) : bool
    {
        if (!preg_match(
            '/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/',
            $value->getName()
        )) {
            $this->_addMessages([__('This domain address is incorrect')]);
        }

        if ($this->domainConfig->isDomainDisallowed($value->getName())) {
            $this->_addMessages([__('This domain address can\'t be used')]);
        }

        return empty($this->getMessages());
    }
}
