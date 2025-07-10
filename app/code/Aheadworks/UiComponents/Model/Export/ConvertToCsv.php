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

namespace Aheadworks\UiComponents\Model\Export;

use Aheadworks\UiComponents\Model\Component\MassAction\Filter;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DocumentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Model\Export\MetadataProvider;
use Psr\Log\LoggerInterface;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @param DocumentFactory $documentFactory
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param LoggerInterface $logger
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        private readonly DocumentFactory $documentFactory,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        private readonly LoggerInterface $logger,
        int $pageSize = 200
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();
        $listingComponent = $this->filter->getListingComponent();
        $dataProvider = $component->getContext()->getDataProvider();
        $listingProvider = $listingComponent->getContext()->getDataProvider();
        if ($dataProvider instanceof $listingProvider) {
            $name = sha1(microtime());
            $file = 'export/'. $component->getName() . $name . '.csv';

            $this->filter->prepareComponent($component);
            $this->filter->applySelectionOnTargetProvider();
            $dataSource = $component->getContext()->getDataSourceData($component);
            $fields = $this->metadataProvider->getFields($component);
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $stream->writeCsv($this->metadataProvider->getHeaders($component));
            $i = 1;
            $searchCriteria = $dataProvider->getSearchCriteria()
                ->setCurrentPage($i)
                ->setPageSize($this->pageSize);
            $totalCount = (int) $dataProvider->getSearchResult()->getTotalCount();
            while ($totalCount > 0) {
                $items = $dataSource[$component->getContext()->getDataProvider()->getName()]['config']['data']['items']
                    ?? [];
                foreach ($items as $item) {
                    $document = $this->documentFactory->create();
                    $document->addData($item);
                    $this->metadataProvider->convertDate($document, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($document, $fields, []));
                }
                $searchCriteria->setCurrentPage(++$i);
                $totalCount = $totalCount - $this->pageSize;
            }
            $stream->unlock();
            $stream->close();

            return [
                'type' => 'filename',
                'value' => $file,
                'rm' => true  // can delete file after use
            ];
        } else {
            $this->logger->error(__('%1 doesn\'t implement %2', $dataProvider::class, $listingProvider::class));
            throw new LocalizedException(__('Export error'));
        }

        return [];
    }
}
