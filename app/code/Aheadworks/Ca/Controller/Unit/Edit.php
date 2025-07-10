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

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\Page as ResultPage;

class Edit extends AbstractUnitAction
{
    /**
     * check is entity belongs to customer
     */
    public const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * Edit Constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UnitRepositoryInterface $unitRepository
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        UnitRepositoryInterface $unitRepository,
        JsonFactory $jsonResultFactory,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession, $unitRepository, $jsonResultFactory);
    }

    /**
     * Edit action
     *
     * @return ResultPage
     */
    public function execute(): ResultPage
    {
        $unitId = $this->getEntityIdByRequest();
        if ($unitId) {
            $unit = $this->getEntity();
        }
        $this->setCompanyParam();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($unitId
            ? __('Edit Unit %1', $unit->getUnitTitle()) : __('Unit Information'));

        return $resultPage;
    }
}
