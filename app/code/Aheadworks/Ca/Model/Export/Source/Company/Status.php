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

namespace Aheadworks\Ca\Model\Export\Source\Company;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Aheadworks\Ca\Model\Source\Company\Status as StatusSource;

/**
 * Status source
 */
class Status extends AbstractSource
{
    /**
     * @param StatusSource $statusSource
     */
    public function __construct(
        private readonly StatusSource $statusSource
    ) {
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        return $this->statusSource->toOptionArray();
    }
}
