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
namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface HistoryLogInterface
 * @api
 */
interface HistoryLogInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const COMPANY_ID = 'company_id';
    const IP = 'ip';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_ID = 'customer_id';
    const TIME = 'time';
    const PERFORMED_ACTION = 'performed_action';
    const VALUES_SET_TO = 'values_set_to';
    const ENTITY_TYPE = 'entity_type';
    const ENTITY_ID= 'entity_id';
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
     * @return int
     */
    public function getCompanyId();

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get IP
     *
     * @return int
     */
    public function getIp();

    /**
     * Set IP
     *
     * @param int $ip
     * @return $this
     */
    public function setIp($ip);

    /**
     * Get Customer Name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set Customer Name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get Customer ID
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Time
     *
     * @return string
     */
    public function getTime(): string;

    /**
     * Set Time
     *
     * @param string $time
     * @return $this
     */
    public function setTime($time);

    /**
     * Get Performed Action
     *
     * @return string
     */
    public function getPerformedAction(): string;

    /**
     * Set Performed Action
     *
     * @param string $performedAction
     * @return $this
     */
    public function setPerformedAction($performedAction);

    /**
     * Get Values Set To
     *
     * @return string
     */
    public function getValuesSetTo(): string;

    /**
     * Set Values Set To
     *
     * @param string $valuesSetTo
     * @return $this
     */
    public function setValuesSetTo($valuesSetTo);

    /**
     * Get Entity Type
     *
     * @return string
     */
    public function getEntityType(): string;

    /**
     * Set Entity Type
     *
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * Get Entity ID
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set Entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\HistoryLogExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\HistoryLogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\HistoryLogExtensionInterface $extensionAttributes
    );
}
