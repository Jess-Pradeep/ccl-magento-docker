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

namespace Aheadworks\Ctq\ViewModel\Customer;

use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\ProductMetadataInterface;
use Aheadworks\Ctq\Model\Config;

class FileUploader implements ArgumentInterface
{
    /**
     * Magento version 2.4.7 to check loader template
     */
    private const MAGENTO_VERSION_247 = '2.4.7';

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param UrlInterface $url
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        private readonly ArrayManager $arrayManager,
        private readonly Config $config,
        private readonly UrlInterface $url,
        private readonly ProductMetadataInterface $productMetadata
    ) {
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @param array $jsLayout
     * @return array
     */
    public function prepareJsLayout($jsLayout)
    {
        $fileUploaderPath = $this->arrayManager->findPath('awCtqFileUploader', $jsLayout);
        if ($fileUploaderPath) {
            $fileUploaderLayout = $this->arrayManager->get($fileUploaderPath, $jsLayout);
            $fileUploaderLayout['uploaderConfig'] = [
                'url' => $this->getFileUploadUrl()
            ];
            $dataToMerge = [
                'maxFileSize' => $this->config->getMaxUploadFileSize(),
                'allowedExtensions' => $this->config->getAllowFileExtensions(),
                'notice' => $this->getNotice()
            ];

            $magentoVersion = $this->productMetadata->getVersion();
            if (version_compare($magentoVersion, self::MAGENTO_VERSION_247, '>=')) {
                $dataToMerge['template'] = 'Aheadworks_Ctq/form/element/uploader/247-uploader';
            }

            $fileUploaderLayout = array_merge(
                $fileUploaderLayout,
                $dataToMerge
            );
            $jsLayout = $this->arrayManager->merge($fileUploaderPath, $jsLayout, $fileUploaderLayout);
        }
        return $jsLayout;
    }

    /**
     * Retrieve file upload url
     *
     * @return string
     */
    private function getFileUploadUrl()
    {
        return $this->url->getUrl('aw_ctq/quote/upload', ['_secure' => true]);
    }

    /**
     * Retrieve notice
     *
     * @return Phrase|string
     */
    private function getNotice()
    {
        if (!empty($this->config->getAllowFileExtensions())) {
            $fileTypes = implode(', ', $this->config->getAllowFileExtensions());
            return __('The following file types are allowed: %1', $fileTypes);
        }

        return '';
    }
}
