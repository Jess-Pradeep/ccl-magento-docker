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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\ViewModel\Admin\System\Config;

use Aheadworks\CreditLimit\Model\Serialize\Factory as SerializeFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Import
 */
class Import implements ArgumentInterface
{
    /**
     * Import constructor.
     *
     * @param SerializerInterface $serializer
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private SerializerInterface $serializer,
        private RequestInterface $request,
        private UrlInterface $urlBuilder
    ) {
    }

    /**
     * Get script options
     *
     * @return bool|string
     */
    public function getScriptOptions()
    {
        $params = [
            'form' => [
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'action' => $this->getImportUrl()
            ],
            'sampleFilesBaseUrl' => $this->getSampleFilesBaseUrl()
        ];

        return $this->serializer->serialize($params);
    }

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'aw_credit_limit/import/start',
            [
                '_current' => true,
                '_secure' => $this->request->isSecure()
            ]
        );
    }

    /**
     * Get Sample Files Base Url Url
     *
     * @return string
     */
    public function getSampleFilesBaseUrl(): string
    {
        return $this->urlBuilder->getUrl(
            '*/import/download/',
            ['filename' => 'entity-name']
        );
    }
}
