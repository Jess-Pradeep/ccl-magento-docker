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

namespace Aheadworks\Ca\Model\Company\Address\Shared\Action\Processor;

use Aheadworks\Ca\Api\CompanySharedAddressManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class UpdateAddress implements ProcessorInterface
{
    /**
     * @param CompanySharedAddressManagementInterface $companySharedAddressService
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CompanySharedAddressManagementInterface $companySharedAddressService,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Process with company shared addresses
     *
     * @param int $companyId
     * @param int|null $rootAddressId
     * @return void
     */
    public function process(int $companyId, ?int $rootAddressId = null): void
    {
        try {
            $this->companySharedAddressService->updateCompanyAdminAddressToAllUsers($companyId, $rootAddressId);
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
