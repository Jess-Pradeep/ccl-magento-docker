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

namespace Aheadworks\Ca\Ui\Component\Listing\MassAction\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

/**
 * Provide company options
 */
class Options implements \JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AlertMessageResolver
     */
    private $alertMessageResolver;

    /**
     * @param UrlInterface $urlBuilder
     * @param CompanyRepositoryInterface $companyRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AlertMessageResolver $alertMessageResolver
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CompanyRepositoryInterface $companyRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AlertMessageResolver $alertMessageResolver
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->companyRepository = $companyRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->alertMessageResolver = $alertMessageResolver;
    }

    /**
     * Get action options
     *
     * @return array
     * @throws LocalizedException
     */
    public function jsonSerialize() : array
    {
        if ($this->options === null) {
            $companyList = $this->companyRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $options = [];
            foreach ($companyList as $company) {
                $companyId = $company->getId();
                $options[$companyId] = [
                    'id' => $companyId,
                    'type' => 'company_' . $companyId,
                    'label' => $company->getName(),
                    'url' => $this->prepareUrl($company),
                    'confirm' => $this->prepareConfirmAlert()
                ];
            }

            $this->options = array_values($options);
        }

        return $this->options;
    }

    /**
     * Prepare URL
     *
     * @param CompanyInterface $company
     * @return string
     */
    private function prepareUrl(CompanyInterface $company) : string
    {
        return $this->urlBuilder->getUrl('aw_ca/company/MassAssignCustomer', ['id' => $company->getId()]);
    }

    /**
     * Prepare confirm alert
     *
     * @return array
     */
    private function prepareConfirmAlert() : array
    {
        $message = $this->alertMessageResolver->getMessage();

        return [
            'title' => __('Assign to the Company'),
            'message' => $message
        ];
    }
}
