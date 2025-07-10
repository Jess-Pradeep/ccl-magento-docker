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

namespace Aheadworks\RequisitionLists\Ui\Component\Form\Element;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\App\ProductMetadataInterface;

class FileUpload extends Field
{
    /**
     * Magento version 2.4.7 to check image template
     */
    private const MAGENTO_VERSION_247 = '2.4.7';

    /**
     * FileUpload constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductMetadataInterface $productMetadata
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly ProductMetadataInterface $productMetadata,
        array $components,
        array $data
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Add js listener to import file
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepare()
    {
        parent::prepare();
        $magentoVersion = $this->productMetadata->getVersion();
        if (version_compare($magentoVersion, self::MAGENTO_VERSION_247, '>=')) {
            $this->_data['config']['template'] = 'Aheadworks_RequisitionLists/form/element/uploader/uploader';
        }
    }
}
