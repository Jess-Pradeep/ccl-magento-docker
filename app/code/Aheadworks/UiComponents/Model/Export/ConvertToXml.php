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

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Convert\Excel;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\DataProvider\DocumentFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\SearchResultIterator;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use Psr\Log\LoggerInterface;

class ConvertToXml extends \Magento\Ui\Model\Export\ConvertToXml
{
    /**
     * @param DocumentFactory $documentFactory
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param ExcelFactory $excelFactory
     * @param SearchResultIteratorFactory $iteratorFactory
     * @param LoggerInterface $logger
     * @throws FileSystemException
     */
    public function __construct(
        private readonly DocumentFactory $documentFactory,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ExcelFactory $excelFactory,
        SearchResultIteratorFactory $iteratorFactory,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider, $excelFactory, $iteratorFactory);
    }

    /**
     * Returns XML file
     *
     * @return array
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function getXmlFile()
    {
        $component = $this->filter->getComponent();
        $listingComponent = $this->filter->getListingComponent();
        $dataProvider = $component->getContext()->getDataProvider();
        $listingProvider = $listingComponent->getContext()->getDataProvider();

        if ($dataProvider instanceof $listingProvider) {
            $component = $this->filter->getComponent();

            $name = sha1(microtime());
            $file = 'export/'. $component->getName() . $name . '.xml';

            $this->filter->prepareComponent($component);
            $this->filter->applySelectionOnTargetProvider();
            $component->getContext()->getDataProvider()->setLimit(0, 0);
            $dataSource = $component->getContext()->getDataSourceData($component);
            $searchResult = $component->getContext()->getDataProvider()->getSearchResult();

            $i = 1;
            $totalCount = (int)$searchResult->getTotalCount();
            $searchResultItems = [];

            $items = $dataSource[$component->getContext()->getDataProvider()->getName()]['config']['data']['items']
                ?? [];
            foreach ($items as $item) {
                $document = $this->documentFactory->create();
                $document->addData($item);
                $searchResultItems[] = $document;
            }

            $this->prepareItems($component->getName(), $searchResultItems);

            $searchResultIterator = $this->iteratorFactory->create(['items' => $searchResultItems]);

            $excel = $this->excelFactory->create(
                [
                    'iterator' => $searchResultIterator,
                    'rowCallback'=> [$this, 'getRowData'],
                ]
            );

            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();

            $excel->setDataHeader($this->metadataProvider->getHeaders($component));
            $excel->write($stream, $component->getName() . '.xml');

            $stream->unlock();
            $stream->close();
        } else {
            $this->logger->error(__('%1 doesn\'t implement %2', $dataProvider::class, $listingProvider::class));
            throw new LocalizedException(__('Export error'));
        }

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
