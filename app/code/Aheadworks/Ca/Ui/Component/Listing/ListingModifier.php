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

namespace Aheadworks\Ca\Ui\Component\Listing;

use Magento\Ui\Component\Listing as ListingComponent;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;

class ListingModifier extends ListingComponent
{
    /**
     * @var array
     */
    protected array $componentNamesToModify = [];

    /**
     * @param ContextInterface $context
     * @param array $componentNamesToModify
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        array $componentNamesToModify = [],
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->componentNamesToModify = $componentNamesToModify;
    }

    /**
     * Prepare component configuration
     *
     * @throws /Exception
     */
    public function prepare()
    {
        if (!empty($this->componentNamesToModify)) {
            $this->modifyComponents($this);
        }
        parent::prepare();
    }

    /**
     * Modify components according to the list
     *
     * @param UiComponentInterface $component
     * @return $this
     */
    private function modifyComponents(UiComponentInterface $component): self
    {
        $childComponents = $component->getChildComponents();
        if (!empty($childComponents)) {
            foreach ($childComponents as $child) {
                $this->modifyComponents($child);
            }
        }

        if (isset($this->componentNamesToModify[$component->getName()])) {
            $config = $component->getData('config');
            $config = array_merge($config, $this->componentNamesToModify[$component->getName()]);
            $component->setData('config', $config);
        }

        return $this;
    }
}
