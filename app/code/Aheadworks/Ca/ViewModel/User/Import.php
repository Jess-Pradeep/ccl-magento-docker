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
namespace Aheadworks\Ca\ViewModel\User;

use Aheadworks\Ca\Model\Url;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Import implements ArgumentInterface
{
    /**
     * @param Url $url
     */
    public function __construct(
        private readonly Url $url
    ) {
    }

    /**
     * Get download sample link
     *
     * @return string
     */
    public function getDownloadSampleLink(): string
    {
        return $this->url->getUrlToDownloadSampleFileForUserImport();
    }

    /**
     * Get start link
     *
     * @return string
     */
    public function getStartImportLink(): string
    {
        return $this->url->getUrlToStartUserImport();
    }
}
