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
namespace Aheadworks\Ctq\Model\Layout\Customer\Quote\RequestPopup\Processor;

use Aheadworks\Ctq\Model\Layout\ProcessorInterface;
use Aheadworks\Ctq\ViewModel\Customer\FileUploader as FileUploaderViewModel;

/**
 * Class FileUploader
 *
 * @package Aheadworks\Ctq\Model\Layout\Customer\Quote\RequestPopup\Processor
 */
class FileUploader implements ProcessorInterface
{
    /**
     * @var FileUploaderViewModel
     */
    private $fileUploaderViewModel;

    /**
     * @param FileUploaderViewModel $fileUploaderViewModel
     */
    public function __construct(
        FileUploaderViewModel $fileUploaderViewModel
    ) {
        $this->fileUploaderViewModel = $fileUploaderViewModel;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $jsLayout = $this->fileUploaderViewModel->prepareJsLayout($jsLayout);
        return $jsLayout;
    }
}
