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
namespace Aheadworks\UiComponents\Controller\Bookmark;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterfaceFactory;

/**
 * Class Save
 * @package Aheadworks\UiComponents\Controller\Bookmark
 */
class Save extends \Magento\Ui\Controller\Adminhtml\Bookmark\Save
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json|null
     */
    protected $serializer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        UiComponentFactory $factory,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        BookmarkInterfaceFactory $bookmarkFactory,
        UserContextInterface $userContext,
        DecoderInterface $jsonDecoder,
        ?\Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context, $factory, $bookmarkRepository, $bookmarkManagement,
            $bookmarkFactory, $userContext, $jsonDecoder, $this->serializer);
    }
}
