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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Model\Import;

class ProcessingErrorAggregator
{
    public const ERROR_CODE_ROWS_INVALID = 'rowsInvalid';

    /**
     * @var array
     */
    private array $items = [];

    /**
     * @var array
     */
    protected array $errorMessageTemplates = [
        self::ERROR_CODE_ROWS_INVALID => 'Rows: "%1" are invalid'
    ];

    /**
     * Add error
     *
     * @param int $rowNumber
     * @return void
     */
    public function addError(int $rowNumber): void
    {
        $this->items['rows'][] = $rowNumber;
    }

    /**
     * Generate error message for invalid rows
     *
     * @param string $errorCode
     * @return string|null
     */
    public function generateErrorMessageWithContainer(string $errorCode = self::ERROR_CODE_ROWS_INVALID): ?string
    {
        $message = '';
        if (!empty($this->errorMessageTemplates[$errorCode]) && !empty($this->items['rows'])) {
            $message = __($this->errorMessageTemplates[$errorCode], implode(', ', $this->items['rows']));
        }

        return $message ? sprintf('<div>%s</div>', $message) : null;
    }

    /**
     * Clear all data
     *
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
    }
}
