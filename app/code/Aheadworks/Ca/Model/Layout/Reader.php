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
namespace Aheadworks\Ca\Model\Layout;

use Magento\Framework\App\Area;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\App\State as AppState;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

/**
 * Class Reader
 *
 * @package Aheadworks\Ca\Model\Layout
 */
class Reader
{
    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var DefinitionFetcher
     */
    private $definitionFetcher;

    /**
     * @var CollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * @param DesignInterface $design
     * @param AppState $appState
     * @param DefinitionFetcher $definitionFetcher
     * @param CollectionFactory $themeCollectionFactory
     */
    public function __construct(
        DesignInterface $design,
        AppState $appState,
        DefinitionFetcher $definitionFetcher,
        CollectionFactory $themeCollectionFactory
    ) {
        $this->design = $design;
        $this->appState = $appState;
        $this->definitionFetcher = $definitionFetcher;
        $this->themeCollectionFactory = $themeCollectionFactory;
    }

    /**
     * Read frontend layout
     *
     * @param array|string $handles
     * @param string $xpath
     * @return array
     * @throws \Exception
     */
    public function readFromFrontend($handles, $xpath)
    {
        if ($this->appState->getAreaCode() != Area::AREA_FRONTEND) {
            /** @var Collection $themeCollection */
            $themeCollection = $this->themeCollectionFactory->create();
            $themeCollection->addAreaFilter(Area::AREA_FRONTEND);
            $this->design->setDesignTheme($themeCollection->getFirstItem());
        }

        return $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            function () use ($handles, $xpath) {
                return $this->definitionFetcher->fetchArgs($handles, $xpath);
            }
        );
    }
}
