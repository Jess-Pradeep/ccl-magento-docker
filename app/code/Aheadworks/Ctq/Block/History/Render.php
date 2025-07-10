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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ctq\Block\History;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Block\History\Action\DefaultRenderer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\RendererList;
use Magento\Framework\View\Element\Template;

/**
 * History renderer
 *
 * @package Aheadworks\Ctq\Block\History
 * @method Render setHistory(HistoryInterface $history)
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 * @method \Aheadworks\Ctq\ViewModel\History\Provider getHistoryProviderViewModel()
 * @method \Aheadworks\Ctq\ViewModel\History\History getHistoryViewModel()
 * @method bool|null getIsEmailForSeller()
 * @method Render setIsEmailForSeller(bool $value)
 */
class Render extends Template
{
    /**
     * Retrieve action html
     *
     * @param HistoryActionInterface $action
     * @return string
     * @throws LocalizedException
     */
    public function getActionHtml(HistoryActionInterface $action): string
    {
        /** @var DefaultRenderer $block */
        $block = $this->getActionRenderer($action->getType());
        $block
            ->setAction($action)
            ->setHistory($this->getHistory())
            ->setIsEmailForSeller($this->getIsEmailForSeller())
            ->setQuoteViewModel($this->getQuoteViewModel())
            ->setHistoryViewModel($this->getHistoryViewModel());

        return $block->toHtml();
    }

    /**
     * Retrieve action renderer
     *
     * @param string $type
     * @return AbstractBlock
     */
    public function getActionRenderer(string $type): AbstractBlock
    {
        /** @var RendererList $rendererList */
        $rendererList = $this->getChildBlock('action.renderer.list');
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }

        $renderer = $rendererList->getRenderer($type, 'default');
        $renderer->setRenderedBlock($this);

        return $renderer;
    }

    /**
     * Retrieve history
     *
     * @return HistoryInterface
     * @throws LocalizedException
     */
    public function getHistory(): HistoryInterface
    {
        $history = $this->getData('history');
        if ($history !== null) {
            return $history;
        }

        $historyId = (int)$this->getData('history_id');
        $history = $historyId
            ? $this->getHistoryProviderViewModel()->getHistoryItem($historyId)
            : $this->getHistoryProviderViewModel()->getEmptyHistoryItem();
        $this->setData('history', $history);

        return $history;
    }
}
