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

namespace Aheadworks\QuickOrder\Api\Data;

interface CustomOptionFileInterface
{
    /**#@+
     * Constants
     */
    const TYPE = 'type';
    const TITLE = 'title';
    const QUOTE_PATH = 'quote_path';
    const ORDER_PATH = 'order_path';
    const FULLPATH = 'fullpath';
    const SIZE = 'size';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const SECRET_KEY = 'secret_key';
    /**#@-*/

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self;

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self;


    /**
     * Get quote path
     *
     * @return string|null
     */
    public function getQuotePath(): ?string;

    /**
     * Set quote path
     *
     * @param string $quotePath
     * @return $this
     */
    public function setQuotePath(string $quotePath): self;

    /**
     * Get order path
     *
     * @return string|null
     */
    public function getOrderPath(): ?string;

    /**
     * Set order path
     *
     * @param string $orderPath
     * @return $this
     */
    public function setOrderPath(string $orderPath): self;

    /**
     * Get full path
     *
     * @return string|null
     */
    public function getFullPath(): ?string;

    /**
     * Set full path
     *
     * @param string $fullPath
     * @return $this
     */
    public function setFullPath(string $fullPath): self;

    /**
     * Get size
     *
     * @return string|null
     */
    public function getSize(): ?string;

    /**
     * Set size
     *
     * @param string $size
     * @return $this
     */
    public function setSize(string $size): self;

    /**
     * Get width
     *
     * @return int|null
     */
    public function getWidth(): ?int;

    /**
     * Set width
     *
     * @param string $width
     * @return $this
     */
    public function setWidth(string $width): self;

    /**
     * Get height
     *
     * @return int|null
     */
    public function getHeight(): ?int;

    /**
     * Set height
     *
     * @param string $height
     * @return $this
     */
    public function setHeight(string $height): self;

    /**
     * Get secret key
     *
     * @return string|null
     */
    public function getSecretKey(): ?string;

    /**
     * Set secret key
     *
     * @param string $secretKey
     * @return $this
     */
    public function setSecretKey(string $secretKey): self;
}
