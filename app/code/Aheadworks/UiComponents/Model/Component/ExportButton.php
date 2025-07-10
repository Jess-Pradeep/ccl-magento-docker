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
 * @package    UiComponents
 * @version    1.0.5
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\UiComponents\Model\Component;

use Aheadworks\UiComponents\Model\Export\Options\ConfigProcessorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\ExportButton as NativeExportButton;

class ExportButton extends NativeExportButton
{
    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param ConfigProcessorInterface $prepareConfigProcessor
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                          $context,
        UrlInterface                              $urlBuilder,
        private readonly ConfigProcessorInterface $prepareConfigProcessor,
        array                                     $components = [],
        array                                     $data = []
    ) {
        parent::__construct($context, $urlBuilder, $components, $data);
    }

    /**
     * @return void
     */
    public function prepare()
    {
        $context = $this->getContext();
        $config = $this->prepareConfigProcessor->prepare($this->getData('config'));
        $this->setData('config', $config);

        parent::prepare();
    }
}
