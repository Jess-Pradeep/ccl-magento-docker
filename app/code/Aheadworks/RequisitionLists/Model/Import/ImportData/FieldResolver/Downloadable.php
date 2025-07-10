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

namespace Aheadworks\RequisitionLists\Model\Import\ImportData\FieldResolver;

use Aheadworks\RequisitionLists\Model\Import\ImportData\FieldResolverInterface;

class Downloadable implements FieldResolverInterface
{
    /**
     * @var array|string[]
     */
    private array $fieldsMap = [
        'links'
    ];

    /**
     * Resolve field data
     *
     * @param string $data
     * @param string $field
     * @return array
     */
    public function resolveData(string $data, string $field): array
    {
        $result = [];

        try {
            if (in_array($field, $this->fieldsMap)) {
                $result = explode(',', $data) ?? [];
            }
        } catch (\Exception|\Error) {
        }

        return $result;
    }
}
