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

namespace Aheadworks\Ca\Model\Export;

use Aheadworks\Ca\Model\Export\Config as ExportConfig;

/**
 * Provides list of columns
 */
class ColumnProvider implements ColumnProviderInterface
{
    /**
     * @var array|null
     */
    private ?array $columns = null;

    /**
     * @param ExportConfig $exportConfig
     * @param string $entityType
     */
    public function __construct(
        private readonly ExportConfig $exportConfig,
        private readonly string $entityType
    ) {
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->exportConfig->prepareCsvHeaders($this->entityType);
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns(): array
    {
        if ($this->columns === null) {
            $this->columns = $this->exportConfig->prepareCsvColumns($this->entityType);
        }

        return $this->columns;
    }
}
