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
namespace Aheadworks\Ca\Controller\Company\Domain;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Controller\Company\AbstractCompanyAction;
use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Source\Company\Domain\AdminType;

/**
 * Class Delete
 *
 * @package Aheadworks\Ca\Controller\Company\Domain
 */
class Delete extends AbstractCompanyAction
{
    /**
     * {@inheritdoc}
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CommandInterface
     */
    private $deleteCommand;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CompanyRepositoryInterface $companyRepository
     * @param CommandInterface $deleteCommand
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CompanyRepositoryInterface $companyRepository,
        CommandInterface $deleteCommand
    ) {
        parent::__construct($context, $customerSession, $companyRepository);
        $this->deleteCommand = $deleteCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $params = $this->getRequest()->getParams();

        try {
            $params[CompanyDomainInterface::REQUESTED_BY] = AdminType::COMPANY_ADMIN;
            $this->deleteCommand->execute($params);
            $result = [
                'error'     => false,
                'message'   => __('Success')
            ];
        } catch (\Exception $e) {
            $result = [
                'error'     => true,
                'message'   => __($e->getMessage())
            ];
        }

        return $resultJson->setData($result);
    }
}
