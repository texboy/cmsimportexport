<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Block;

use Magento\Cms\Model\ResourceModel\Block\Collection as EntityCollection;
use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\ImportExport\Model\Export;
use Magento\InventoryImportExport\Model\Export\ColumnProviderInterface;
use Texboy\CmsImportExport\Model\Export\Filter\FilterProcessorAggregator;

/**
 * @inheritdoc
 */
class BlockCollectionFactory implements BlockCollectionFactoryInterface
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var FilterProcessorAggregator
     */
    private $filterProcessor;

    /**
     * @var ColumnProviderInterface
     */
    private $columnProvider;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param FilterProcessorAggregator $filterProcessor
     * @param ColumnProviderInterface $columnProvider
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        FilterProcessorAggregator $filterProcessor,
        ColumnProviderInterface $columnProvider
    ) {
        $this->objectManager = $objectManager;
        $this->filterProcessor = $filterProcessor;
        $this->columnProvider = $columnProvider;
    }

    /**
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return EntityCollection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): EntityCollection
    {
        /** @var EntityCollection $collection */
        $collection = $this->objectManager->create(EntityCollection::class);
        $columns = $this->columnProvider->getColumns($attributeCollection, $filters);
        $collection->addFieldToSelect($columns);
        foreach ($this->retrieveFilterData($filters) as $columnName => $value) {
            $attributeDefinition = $attributeCollection->getItemById($columnName);
            if (!$attributeDefinition) {
                throw new LocalizedException(__(
                    'Given column name "%columnName" is not present in collection.',
                    ['columnName' => $columnName]
                ));
            }

            $type = $attributeDefinition->getData('backend_type');
            if (!$type) {
                throw new LocalizedException(__(
                    'There is no backend type specified for column "%columnName".',
                    ['columnName' => $columnName]
                ));
            }
            $this->filterProcessor->process($type, $collection, $columnName, $value);
        }
        return $collection;
    }

    /**
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters)
    {
        return array_filter(
            $filters[Export::FILTER_ELEMENT_GROUP] ?? [],
            function ($value) {
                return $value !== '';
            }
        );
    }
}
