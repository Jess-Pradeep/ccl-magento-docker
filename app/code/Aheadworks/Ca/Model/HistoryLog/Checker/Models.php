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

namespace Aheadworks\Ca\Model\HistoryLog\Checker;

/**
 * Class Models
 */
class Models
{
    /**
     * @var string[]
     */
    private $models;

    /**
     * @param string[] $models
     */
    public function __construct(
        array $models = []
    ) {
        $this->models = $models;
    }

    /**
     * Check Model Belong to Allowed Models List to proceed
     *
     * @param string $resourceName
     * @return bool
     */
    public function checkModelBelongToAllowedModelsList(string $resourceName): bool
    {
        return in_array($resourceName, $this->models);
    }
}
