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
namespace Aheadworks\Ca\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\Ca\Setup\SampleData\Installer as SampleDataInstaller;

class InstallSampleData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var SampleDataExecutor
     */
    private $sampleDataExecutor;

    /**
     * @var SampleDataInstaller
     */
    private $sampleDataInstaller;

    /**
     * @param SampleDataExecutor $sampleDataExecutor
     * @param SampleDataInstaller $sampleDataInstaller
     */
    public function __construct(
        SampleDataExecutor $sampleDataExecutor,
        SampleDataInstaller $sampleDataInstaller
    ) {
        $this->sampleDataExecutor = $sampleDataExecutor;
        $this->sampleDataInstaller = $sampleDataInstaller;
    }

    /**
     * Install sample data
     */
    public function apply(): self
    {
        $this->sampleDataExecutor->exec($this->sampleDataInstaller);

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
