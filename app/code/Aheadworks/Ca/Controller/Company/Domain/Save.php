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
use Aheadworks\Ca\Model\Source\Company\Domain\AdminType;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Ca\Controller\Company\Domain
 */
class Save extends AbstractCompanyAction
{
    /**
     * {@inheritdoc}
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CompanyRepositoryInterface $companyRepository
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CompanyRepositoryInterface $companyRepository,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context, $customerSession, $companyRepository);
        $this->saveCommand = $saveCommand;
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
            $this->saveCommand->execute($params);
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
