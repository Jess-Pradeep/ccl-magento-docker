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
namespace Aheadworks\Ca\Model\Data\Validator\Company\Domain;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Company\Domain\Search\Builder as DomainSearchBuilder;

/**
 * Class Uniqueness
 *
 * @package Aheadworks\Ca\Model\Data\Validator\Company\Domain
 */
class Uniqueness extends AbstractValidator
{
    /**
     * @var DomainSearchBuilder
     */
    private $domainSearchBuilder;

    /**
     * @param DomainSearchBuilder $domainSearchBuilder
     */
    public function __construct(
        DomainSearchBuilder $domainSearchBuilder
    ) {
        $this->domainSearchBuilder = $domainSearchBuilder;
    }

    /**
     * Returns true if domain is valid
     *
     * @param CompanyDomainInterface $domain
     * @return bool
     * @throws LocalizedException
     */
    public function isValid($domain)
    {
        $this->domainSearchBuilder->addNameFilter($domain->getName());
        $domainList = $this->domainSearchBuilder->searchDomains();

        if (!$domain->getId() && count($domainList) > 0) {
            $this->_addMessages([__('This domain already exists')]);
        }

        return empty($this->getMessages());
    }
}
