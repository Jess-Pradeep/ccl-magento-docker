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

namespace Aheadworks\Ca\Controller\Unit;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Controller\AbstractCustomerAction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class AbstractUnitAction extends AbstractCustomerAction
{
    /**
     * AbstractUnitAction Constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UnitRepositoryInterface $unitRepository
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context, $customerSession);
    }

    /**
     * Retrieve unit
     *
     * @return UnitInterface
     * @throws NotFoundException
     */
    protected function getEntity()
    {
        try {
            $id = $this->getEntityIdByRequest();
            $entity = $this->unitRepository->get($id);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $entity;
    }

    /**
     * Check if entity belongs to current customer
     *
     * @return bool
     * @throws NotFoundException
     */
    protected function isEntityBelongsToCustomer() : bool
    {
        if (!$this->isForwardAction(['new'])) {
            $unit = $this->getEntity();
            if ($this->getCurrentCompanyId() != $unit->getCompanyId()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set Company Param
     *
     * @return void
     */
    protected function setCompanyParam()
    {
        $this->getRequest()->setParams(['company_id' => $this->getCurrentCompanyId()]);
    }
}
