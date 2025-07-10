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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Aheadworks\CreditLimit\Model\Customer\Backend\BookmarkInstaller;

class CreateBalanceGridBookmark implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var BookmarkInstaller
     */
    private $bookmarkInstaller;

    /**
     * @param BookmarkInstaller $bookmarkInstaller
     */
    public function __construct(
        BookmarkInstaller $bookmarkInstaller
    ) {
        $this->bookmarkInstaller = $bookmarkInstaller;
    }

    /**
     * Create balance grid bookmark
     *
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->bookmarkInstaller->install();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.0.0';
    }
}
