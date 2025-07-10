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
 * @package    UiComponents
 * @version    1.0.5
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\UiComponents\Model\Config\Reader;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for config (per-file)
     *
     * @var ?string
     */
    private ?string $perFileSchema = null;

    /**
     * @var ?string
     */
    private ?string $schema = null;

    /**
     * @var string
     */
    private string $fileName = 'ui_configuration.xsd';

    /**
     * @param Reader $moduleReader
     * @param string|null $fileName
     * @param bool|null $isSchema
     */
    public function __construct(Reader $moduleReader, ?string $fileName, ?bool $isSchema = false)
    {
        $path = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Aheadworks_UiComponents')
        . '/' . $fileName ?? $this->fileName;
        $isSchema
            ? $this->schema = $path
            : $this->perFileSchema = $path;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
