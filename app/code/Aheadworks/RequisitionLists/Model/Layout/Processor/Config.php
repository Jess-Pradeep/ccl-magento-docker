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
namespace Aheadworks\RequisitionLists\Model\Layout\Processor;

use Aheadworks\RequisitionLists\Model\Layout\LayoutProcessorInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\Url;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Config
 * @package Aheadworks\RequisitionLists\Model\Toolbar\Layout\Processor
 */
class Config implements LayoutProcessorInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param ArrayManager $arrayManager
     * @param Provider $provider
     * @param Url $url
     */
    public function __construct(
        ArrayManager $arrayManager,
        Provider $provider,
        Url $url
    ) {
        $this->arrayManager = $arrayManager;
        $this->url = $url;
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $component = 'components/aw_requisition_list_config';
        $jsLayout = $this->arrayManager->merge(
            $component,
            $jsLayout,
            [
                'configureItemUrl' => $this->url->getConfigureItemUrl(),
                'updateItemOptionUrl' => $this->url->getUpdateItemOptionUrl(),
                'removeItemUrl' => $this->url->getRemoveItemUrl(
                    $this->provider->getRequisitionListId()
                ),
            ]
        );

        return $jsLayout;
    }
}
