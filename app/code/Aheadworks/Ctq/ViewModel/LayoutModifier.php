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
namespace Aheadworks\Ctq\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Model\Layout\ProcessorComposite;

/**
 * Class LayoutModifier
 *
 * @package Aheadworks\Ctq\ViewModel
 */
class LayoutModifier implements ArgumentInterface
{
    /**
     * @var ProcessorComposite
     */
    private $layoutProcessor;

    /**
     * @param ProcessorComposite $layoutProcessor
     */
    public function __construct(
        ProcessorComposite $layoutProcessor
    ) {
        $this->layoutProcessor = $layoutProcessor;
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @param array $jsLayout
     * @return array
     */
    public function prepareJsLayout($jsLayout)
    {
        return $this->layoutProcessor->process($jsLayout);
    }
}
