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
namespace Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Mpdf\MpdfException;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Document
 *
 * @package Aheadworks\Ctq\Model\Quote\Export\Exporter\Pdf
 */
class Document extends Mpdf
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     * @param array $config
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        array $config = []
    ) {
        $this->filesystem = $filesystem;
        $config['tempDir'] = $this->filesystem
            ->getDirectoryWrite(DirectoryList::TMP)
            ->getAbsolutePath('aw_ctq/mpdf');
        parent::__construct($config);
    }

    /**
     * Create pdf document from html
     *
     * @param string $html
     * @return string
     */
    public function createFromHtml($html)
    {
        try {
            $this->WriteHTML($html);
            $pdf = $this->Output('', Destination::STRING_RETURN);
        } catch (MpdfException $e) {
            $pdf = '';
        }

        return $pdf;
    }
}
