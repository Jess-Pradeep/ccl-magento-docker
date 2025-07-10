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

namespace Aheadworks\Ca\Ui\Component\Listing\Order\Column;

/**
 * Class Actions
 */
class ActionProvider
{
    /**
     * @var array
     */
    private $actionProviders;

    /**
     * @param array $actionProviders
     */
    public function __construct(
        array $actionProviders = []
    ) {
        $this->actionProviders = $this->sort($actionProviders);
    }

    /**
     * Get actions by order id
     *
     * @param int $orderId
     * @return array
     */
    public function getActionsByOrderId(int $orderId): array
    {
        $link = [];

        foreach ($this->actionProviders as $actionProvider) {
            if (!is_array($actionProvider['class']->getLink($orderId))) {
                continue;
            }
            $link[$actionProvider['actionName']] = $actionProvider['class']->getLink($orderId);
        }

       return $link;
    }

    /**
     * Sorting action providers according to sort order
     *
     * @param array $data
     * @return array
     */
    protected function sort(array $data)
    {
        usort($data, function (array $a, array $b) {
            return $this->getSortOrder($a) <=> $this->getSortOrder($b);
        });

        return $data;
    }

    /**
     * Retrieve sort order from array
     *
     * @param array $variable
     * @return int
     */
    protected function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? $variable['sortOrder'] : 0;
    }
}
