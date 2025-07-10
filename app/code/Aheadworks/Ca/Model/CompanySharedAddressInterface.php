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

namespace Aheadworks\Ca\Model;

interface CompanySharedAddressInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const ROOT_ADDRESS_ID = 'root_address_id';
    public const COMPANY_USER_ID = 'company_user_id';
    public const COMPANY_USER_ADDRESS_ID = 'company_user_address_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get company id
     *
     * @return int|null
     */
    public function getCompanyId(): ?int;

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId(int $companyId): self;

    /**
     * Get root address id
     *
     * @return int|null
     */
    public function getRootAddressId(): ?int;

    /**
     * Set root address id
     *
     * @param int $rootAddressId
     * @return $this
     */
    public function setRootAddressId(int $rootAddressId): self;

    /**
     * Get company user id
     *
     * @return int|null
     */
    public function getCompanyUserId(): ?int;

    /**
     * Set company user id
     *
     * @param int $companyUserId
     * @return $this
     */
    public function setCompanyUserId(int $companyUserId): self;

    /**
     * Get company user address id
     *
     * @return int|null
     */
    public function getCompanyUserAddressId(): ?int;

    /**
     * Set company user address id
     *
     * @param int $addressId
     * @return $this
     */
    public function setCompanyUserAddressId(int $addressId): self;
}
