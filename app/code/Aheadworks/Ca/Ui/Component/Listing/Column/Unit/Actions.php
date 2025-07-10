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

namespace Aheadworks\Ca\Ui\Component\Listing\Column\Unit;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Aheadworks\Ca\Api\UnitRepositoryInterface;

class Actions extends Column
{
    /**
     * Actions Construct
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param UnitRepositoryInterface $unitRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        private readonly UnitRepositoryInterface $unitRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getData('config');
        $indexFieldName = $config['indexField'];
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$indexFieldName])) {
                    $item[$name] = $this->getActionsDataForItem($item);
                }
            }
        }
        return $dataSource;
    }

    /**
     * Retrieve index field name
     *
     * @return string
     */
    protected function getIndexField()
    {
        return $this->getData('config/indexField');
    }

    /**
     * Get actions data
     *
     * @param array $item
     * @return array
     */
    protected function getActionsDataForItem($item)
    {
        $actionsData = [];
        $actionsConfig = $this->getActionsConfig();
        foreach ($actionsConfig as $actionName => $actionConfigData) {
            $currentActionData = $this->getDataForAction($actionConfigData, $item, $actionName);
            if (!empty($currentActionData)) {
                $actionsData[$actionName] = $currentActionData;
            }
        }
        return $actionsData;
    }

    /**
     * Retrieve item actions config
     *
     * @return array
     */
    protected function getActionsConfig()
    {
        return $this->getData('config/actions');
    }

    /**
     * Get action data for specified item id
     *
     * @param array $actionConfigData
     * @param array $itemData
     * @param string $actionName
     * @return array
     */
    protected function getDataForAction($actionConfigData, $itemData, $actionName)
    {
        $action = [];
        $idKey = $actionConfigData['id_key'];
        $id = $itemData[$idKey];
        $config = $this->getData('config');
        $addCompanyIdToUrl = $config['addCompanyIdToUrl'];
        $rootUnit = $this->unitRepository->getCompanyRootUnit($itemData['company_id']);
        if ($id) {
            if ($actionName == 'delete' && $id == $rootUnit->getId()) {
                return $action;
            }
            $urlParams[$this->getParamKey($actionConfigData)] = $id;
            if ($addCompanyIdToUrl) {
                $urlParams['company_id'] = $itemData['company_id'];
            }
            $action = [
                'href' => $this->urlBuilder->getUrl(
                    $actionConfigData['url_route'],
                    $urlParams
                ),
                'label' => $actionConfigData['label']
            ];
            if (isset($actionConfigData['confirm'])
                && isset($actionConfigData['confirm']['title'])
                && isset($actionConfigData['confirm']['message'])
            ) {
                $action['confirm'] = [
                    'title' => $actionConfigData['confirm']['title'],
                    'message' => $actionConfigData['confirm']['message']
                ];
            }
        }

        return $action;
    }

    /**
     * Get param key
     *
     * @param array $actionConfigData
     * @return string
     */
    protected function getParamKey($actionConfigData)
    {
        return isset($actionConfigData['param_key'])
            ? $actionConfigData['param_key']
            : $this->getIndexField();
    }
}
