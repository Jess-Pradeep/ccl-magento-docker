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
namespace Aheadworks\Ca\Block\Adminhtml\Company\Edit\Button;

use Magento\Framework\Phrase;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Model\Company\Checker\IsAllowedToRemoveCompany;

/**
 * Class Delete
 *
 * @package Aheadworks\Ca\Block\Adminhtml\Company\Edit\Button
 */
class Delete extends AbstractButton implements ButtonProviderInterface
{
    /**
     * CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var IsAllowedToRemoveCompany
     */
    private $isAllowedToRemoveCompany;

    /**
     * @param Context $context
     * @param CompanyRepositoryInterface $companyRepository
     * @param IsAllowedToRemoveCompany $isAllowedToRemoveCompany
     */
    public function __construct(
        Context $context,
        CompanyRepositoryInterface $companyRepository,
        IsAllowedToRemoveCompany $isAllowedToRemoveCompany
    ) {
        parent::__construct($context);
        $this->companyRepository = $companyRepository;
        $this->isAllowedToRemoveCompany = $isAllowedToRemoveCompany;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $company = $this->getCompany();
        if ($company && $this->isAllowedToRemoveCompany->check($company)) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . $this->getConfirmMessage()
                    . '\', \'' . $this->getUrl('*/*/delete', ['id' => $company->getId()]) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get company
     *
     * @return CompanyInterface|null
     */
    private function getCompany()
    {
        $company = null;
        $companyId = $this->context->getRequest()->getParam('id');
        if ($companyId) {
            try {
                $company = $this->companyRepository->get($companyId);
            } catch (NoSuchEntityException $exception) {
                $company = null;
            }
        }

        return $company;
    }

    /**
     * Get confirm message
     *
     * @return Phrase
     */
    private function getConfirmMessage()
    {
        return __(
            'Do you want to delete this company permanently? '
            . 'Company data and related sub-accounts will be deleted.'
        );
    }
}
