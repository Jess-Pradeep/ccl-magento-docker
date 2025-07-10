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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Model\Product\Option\CustomOptions\File;

use Aheadworks\QuickOrder\Api\Data\CustomOptionFileInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CustomOptionFile extends AbstractExtensibleModel implements CustomOptionFileInterface
{
    /**
     * Get type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type): CustomOptionFileInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): CustomOptionFileInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get quote path
     *
     * @return string|null
     */
    public function getQuotePath(): ?string
    {
        return $this->getData(self::QUOTE_PATH);
    }

    /**
     * Set quote path
     *
     * @param string $quotePath
     * @return $this
     */
    public function setQuotePath(string $quotePath): CustomOptionFileInterface
    {
        return $this->setData(self::QUOTE_PATH, $quotePath);
    }

    /**
     * Get order path
     *
     * @return string|null
     */
    public function getOrderPath(): ?string
    {
        return $this->getData(self::ORDER_PATH);
    }

    /**
     * Set order path
     *
     * @param string $orderPath
     * @return $this
     */
    public function setOrderPath(string $orderPath): CustomOptionFileInterface
    {
        return $this->setData(self::ORDER_PATH, $orderPath);
    }

    /**
     * Get full path
     *
     * @return string|null
     */
    public function getFullPath(): ?string
    {
        return $this->getData(self::FULLPATH);
    }

    /**
     * Set full path
     *
     * @param string $fullPath
     * @return $this
     */
    public function setFullPath(string $fullPath): CustomOptionFileInterface
    {
        return $this->setData(self::FULLPATH, $fullPath);
    }

    /**
     * Get size
     *
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->getData(self::SIZE);
    }

    /**
     * Set size
     *
     * @param string $size
     * @return $this
     */
    public function setSize(string $size): CustomOptionFileInterface
    {
        return $this->setData(self::SIZE, $size);
    }

    /**
     * Get width
     *
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * Set width
     *
     * @param string $width
     * @return $this
     */
    public function setWidth(string $width): CustomOptionFileInterface
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * Get height
     *
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * Set height
     *
     * @param string $height
     * @return $this
     */
    public function setHeight(string $height): CustomOptionFileInterface
    {
        return $this->setData(self::HEIGHT, $height);
    }

    /**
     * Get secret key
     *
     * @return string|null
     */
    public function getSecretKey(): ?string
    {
        return $this->getData(self::SECRET_KEY);
    }

    /**
     * Set secret key
     *
     * @param string $secretKey
     * @return $this
     */
    public function setSecretKey(string $secretKey): CustomOptionFileInterface
    {
        return $this->setData(self::SECRET_KEY, $secretKey);
    }
}