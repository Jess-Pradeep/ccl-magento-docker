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
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\ProcessorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;


class AwSarpSubscriptionModel implements ProcessorInterface
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
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param RequestInterface $request
     * @param UserResolver $userResolver
     * @param CompanyUserProvider $companyUserProvider
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        RequestInterface $request,
        UserResolver $userResolver,
        CompanyUserProvider $companyUserProvider,
        TimezoneInterface $localeDate
    ) {
        $this->request = $request;
        $this->userResolver = $userResolver;
        $this->companyUserProvider = $companyUserProvider;
        $this->localeDate = $localeDate;
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
                $object->setPerformedAction(__('%1 %2 via Admin Panel', $processor['action'], ucfirst($actionName))->render());
                $actionName = 'save'.ucfirst($actionName);
            } else {
                // For API
                $sarpApi = 'awSarp2/profile';
                $apiUrl = $this->request->getPathInfo() ?? '';
                if (str_contains($apiUrl, $sarpApi)) {
                    $actionName = ucfirst(mb_substr($apiUrl, strrpos($apiUrl, '/') + 1));
                }
                $object->setPerformedAction(__('%1 %2 By API', $processor['action'], str_replace('save','', $actionName ?? ''))->render());
                $actionName = 'save'.$actionName;
            }

            $companyUser = $this->companyUserProvider->getCompanyUserByCustomer((int)$model->getCustomerId());
            if ($companyUser) {
                $object->setCompanyId((int)$companyUser->getCompanyId());
            }
        } else {
            $object->setPerformedAction($processor['action'] . ' ' . str_replace('save','', $actionName ?? ''));
        }

        if ($model->isObjectNew()) {
            $object->setValuesSetTo(__('Add New Subscription')->render());
        } else {
            $newValue = '-';
            $result = array_diff_assoc($model->getData(), $model->getOrigData());
            if ($actionName == 'savePlan') {
                $newValue = $result['plan_name'] ?? '-';
            }
            if ($actionName == 'saveAddress') {
                $addressResult = array_diff_assoc($model->getShippingAddress()->getData(), $model->getShippingAddress()->getOrigData());
                unset($addressResult['profile']);
                $newValue = implode(', ', $addressResult ?? []);
            }
            if ($actionName == 'savePayment') {
                $newValue = $result['payment_method'] ?? '-';
            }
            if ($actionName == 'saveItem') {
                $newValue = 'Updated Product';
            }
            if ($actionName == 'saveNextPaymentDate') {
                $newValue = $this->localeDate->formatDateTime(
                    new \DateTime($result['start_date']),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE,
                    null,
                    null
                );
            }
            $object->setValuesSetTo($newValue);
        }

        $object->setEntityId((int)$model->getProfileId());

        return $object;
    }
}
