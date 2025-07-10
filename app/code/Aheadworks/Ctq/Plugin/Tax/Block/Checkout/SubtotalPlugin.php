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
namespace Aheadworks\Ctq\Plugin\Tax\Block\Checkout;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Tax\Block\Checkout\Subtotal;

class SubtotalPlugin
{
    const TEMPLATE_TO_REPLACE = 'Aheadworks_Ctq::checkout/subtotal.phtml';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * Change template because of typo in the original one
     *
     * @param Subtotal $subject
     * @param string $resultTemplate
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTemplate($subject, $resultTemplate)
    {
        $magentoVersion = $this->productMetadata->getVersion();
        if (version_compare($magentoVersion, '2.4.0', '>=')) {
            $resultTemplate = self::TEMPLATE_TO_REPLACE;
        }

        return $resultTemplate;
    }
}
