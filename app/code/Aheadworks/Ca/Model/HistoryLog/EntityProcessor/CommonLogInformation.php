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

namespace Aheadworks\Ca\Model\HistoryLog\EntityProcessor;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime;
use Aheadworks\Ca\Model\Resolver\UserResolver;
use Aheadworks\Ca\Model\Customer\CompanyUser\Provider as CompanyUserProvider;

class CommonLogInformation implements ProcessorInterface
{
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @var CompanyUserProvider
     */
    private $companyUserProvider;

    /**
     * @param RemoteAddress $remoteAddress
     * @param DateTime $dateTime
     * @param UserResolver $userResolver
     * @param CompanyUserProvider $companyUserProvider
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        UserResolver $userResolver,
        CompanyUserProvider $companyUserProvider
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->userResolver = $userResolver;
        $this->companyUserProvider = $companyUserProvider;
    }

    /**
     * Process data before save
     *
     * @param HistoryLogInterface $object
     * @return HistoryLogInterface
     */
    public function beforeSave(HistoryLogInterface $object): HistoryLogInterface
    {
        $userId = (int)$this->userResolver->getUserId();
        if ($this->userResolver->isUserCustomer()) {
            $companyUser = $this->companyUserProvider->getCompanyUserByCustomer($userId);
            if ($companyUser) {
                $object->setCompanyId((int)$companyUser->getCompanyId());
            }
        }
        $object->setCustomerId($userId);
        $object->setCustomerName($this->userResolver->getUserName($userId));
        $object->setTime($this->dateTime->formatDate(time()));
        $object->setIp((int)ip2long((string)$this->remoteAddress->getRemoteAddress()));

        return $object;
    }
}
