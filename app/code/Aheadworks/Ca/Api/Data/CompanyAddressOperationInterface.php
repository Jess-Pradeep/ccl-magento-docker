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

namespace Aheadworks\Ca\Api\Data;

interface CompanyAddressOperationInterface
{
    /**#@+
     * Constants defined for action service.
     */
    public const ACTION_ADD_SHARED_ADDRESSES = 'add_addresses';
    public const ACTION_UPDATE_SHARED_ADDRESSES = 'update_addresses';
    public const ACTION_DELETE_SHARED_ADDRESSES = 'delete_addresses';

    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    public const COMPANY_ID = 'company_id';
    public const ADDRESS_ID = 'address_id';
    public const ACTION = 'action';

    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId(): int;

    /**
     * Set company id
     *
     * @param int $companyId
     * @return self
     */
    public function setCompanyId(int $companyId): self;

    /**
     * Get address id
     *
     * @return int|null
     */
    public function getAddressId(): ?int;

    /**
     * Set address id
     *
     * @param int $addressId
     * @return self
     */
    public function setAddressId(int $addressId): self;

    /**
     * Get action
     *
     * @return string
     */
    public function getAction(): ?string;

    /**
     * Set action
     *
     * @param string $action
     * @return self
     */
    public function setAction(string $action): self;
}
