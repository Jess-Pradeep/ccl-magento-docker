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

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\HistoryLog\EntityProcessor;
use Aheadworks\Ca\Model\ResourceModel\HistoryLog as HistoryLogResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Api\Data\HistoryLogExtensionInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class HistoryLog
 */
class HistoryLog extends AbstractModel implements HistoryLogInterface
{
    /**
     * @var EntityProcessor
     */
    private $processor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param EntityProcessor $processor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EntityProcessor $processor,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->processor = $processor;
    }

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(HistoryLogResourceModel::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->hasData(self::ID)
            ? (int)$this->getData(self::ID)
            : $this->getData(self::ID);
    }

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, (int)$id);
    }

    /**
     * Get company id
     *
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->hasData(self::COMPANY_ID)
            ? (int)$this->getData(self::COMPANY_ID)
            : $this->getData(self::COMPANY_ID);
    }

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        return $this->setData(self::COMPANY_ID, (int)$companyId);
    }

    /**
     * Get IP
     *
     * @return int
     */
    public function getIp(): int
    {
        return (int)$this->getData(self::IP);
    }

    /**
     * Set IP
     *
     * @param int $ip
     * @return $this
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
    }

    /**
     * Get Customer Name
     *
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Set Customer Name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get Customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, (int)$customerId);
    }

    /**
     * Get Last Updated
     *
     * @return string
     */
    public function getTime(): string
    {
        return $this->getData(self::TIME);
    }

    /**
     * Set Last Updated
     *
     * @param string $time
     * @return $this
     */
    public function setTime($time)
    {
        return $this->setData(self::TIME, $time);
    }

    /**
     * Get Performed Action
     *
     * @return string
     */
    public function getPerformedAction(): string
    {
        return $this->getData(self::PERFORMED_ACTION);
    }

    /**
     * Set Performed Action
     *
     * @param string $performedAction
     * @return $this
     */
    public function setPerformedAction($performedAction)
    {
        return $this->setData(self::PERFORMED_ACTION, $performedAction);
    }

    /**
     * Get Values Set To
     *
     * @return string
     */
    public function getValuesSetTo(): string
    {
        return $this->getData(self::VALUES_SET_TO);
    }

    /**
     * Set Values Set To
     *
     * @param string $valuesSetTo
     * @return $this
     */
    public function setValuesSetTo($valuesSetTo)
    {
        return $this->setData(self::VALUES_SET_TO, $valuesSetTo);
    }

    /**
     * Get Entity Type
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->getData(self::ENTITY_TYPE);
    }

    /**
     * Set Entity Type
     *
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * Get Entity ID
     *
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->hasData(self::ENTITY_ID)
            ? (int)$this->getData(self::ENTITY_ID)
            : $this->getData(self::ENTITY_ID);
    }

    /**
     * Set Entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return HistoryLogExtensionInterface|null
     */
    public function getExtensionAttributes(): ?HistoryLogExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param HistoryLogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(HistoryLogExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Set some data automatically before saving model
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->processor->prepareDataBeforeSave($this);
        return $this;
    }
}
