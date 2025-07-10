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

namespace Aheadworks\Ca\Model\Company\Address\Shared;

use Aheadworks\Ca\Api\Data\CompanyAddressOperationInterface;
use Aheadworks\Ca\Model\Company\Address\Shared\Pool as ActionProcessorPool;

class Consumer
{
    /**
     * Topic name for queue publisher
     */
    public const TOPIC_NAME = 'aw_ca.share_addresses';

    /**
     * @param Pool $actionProcessorPool
     */
    public function __construct(
        private readonly ActionProcessorPool $actionProcessorPool
    ) {}

    /**
     * Process shared addresses for company users
     *
     * @param CompanyAddressOperationInterface $companyAddressOperation
     */
    public function process(CompanyAddressOperationInterface $companyAddressOperation): void
    {
        $companyId = (int)$companyAddressOperation->getCompanyId();
        $action = $companyAddressOperation->getAction();
        $addressId = (int)$companyAddressOperation->getAddressId();
        $processor = $this->actionProcessorPool->getProcessor($action);
        $processor->process($companyId, $addressId);
    }
}
