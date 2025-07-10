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

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Model\HistoryLog\MessagesProcessor;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;
use Aheadworks\Ca\Model\Resolver\UserResolver;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Sarp2\Model\ProfileRepositoryFactory;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\ProcessorInterface;
use Magento\Framework\App\RequestInterface;

class AwSarpSubscriptionEnginePaymentModel implements ProcessorInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @var ProfileRepositoryFactory
     */
    private ProfileRepositoryFactory $profileRepositoryFactory;
    /**
     * @param RequestInterface $request
     * @param UserResolver $userResolver
     * @param CompanyUserProvider $companyUserProvider
     * @param ProfileRepositoryFactory $profileRepositoryFactory
     */
    public function __construct(
        RequestInterface $request,
        UserResolver $userResolver,
        CompanyUserProvider $companyUserProvider,
        ProfileRepositoryFactory $profileRepositoryFactory
    ) {
        $this->request = $request;
        $this->userResolver = $userResolver;
        $this->companyUserProvider = $companyUserProvider;
        $this->profileRepositoryFactory = $profileRepositoryFactory;
    }

    /**
     * Process data before save
     *
     * @param HistoryLogInterface $object
     * @param array $processor
     * @param AbstractModel $model
     * @return HistoryLogInterface
     */
    public function addCustomData(HistoryLogInterface $object, array $processor, AbstractModel $model): HistoryLogInterface
    {
        $actionName = $this->request->getActionName();
        if ($this->userResolver->isUserAdmin()) {
            if ($actionName) {
                //For admin panel
                $object->setPerformedAction(__('%1 via Admin Panel', $processor['action'])->render());
            } else {
                // For API
                $object->setPerformedAction(__('%1 By API', $processor['action'])->render());
            }
            try {
                $schedule = $model->getSchedule();
                $profileRepository = $this->profileRepositoryFactory->create();
                $profile = $profileRepository->get($schedule->getProfileId());
                $companyUser = $this->companyUserProvider->getCompanyUserByCustomer((int)$profile->getCustomerId());
                if ($companyUser) {
                    $object->setCompanyId((int)$companyUser->getCompanyId());
                }
            } catch (\Exception $e) {

            }
        } else {
            $object->setPerformedAction($processor['action']);
        }

        $object->setValuesSetTo($model->getPaymentStatus() ?? '-');
        $object->setEntityId((int)$model->getProfileId());

        return $object;
    }
}
