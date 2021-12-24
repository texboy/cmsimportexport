<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Block;

use Exception;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use \Magento\Cms\Model\ResourceModel\Block\Collection as EntityCollection;
use Magento\InventoryImportExport\Model\Export\ColumnProviderInterface;
use Texboy\CmsImportExport\Model\Export\Block\BlockCollectionFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @inheritdoc
 */
class CmsBlock extends AbstractEntity
{
    /**
     * @var string[]
     */
    protected $_permanentAttributes = [BlockStoreModifier::STORE_ID_COLUMN];

    /**
     * @var AttributeCollectionProvider
     */
    private $attributeCollectionProvider;

    /**
     * @var BlockCollectionFactoryInterface
     */
    private $entityCollectionFactory;

    /**
     * @var ColumnProviderInterface
     */
    private $columnProvider;

    /**
     * @var BlockStoreModifier
     */
    private $blockStoreModifier;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param BlockCollectionFactoryInterface $entityCollectionFactory
     * @param ColumnProviderInterface $columnProvider
     * @paarm BlockStoreModifier $blockStoreModifier
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface             $scopeConfig,
        StoreManagerInterface            $storeManager,
        ExportFactory                    $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        AttributeCollectionProvider      $attributeCollectionProvider,
        BlockCollectionFactoryInterface  $entityCollectionFactory,
        ColumnProviderInterface          $columnProvider,
        BlockStoreModifier               $blockStoreModifier,
        array                            $data = []
    ) {
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->columnProvider = $columnProvider;
        $this->blockStoreModifier = $blockStoreModifier;
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollectionProvider->get();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function export()
    {
        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());
        $collection = $this->entityCollectionFactory->create(
            $this->getAttributeCollection(),
            $this->_parameters
        );
        $items = $this->blockStoreModifier->addStoreIdToBlocks($collection->getItems());
        foreach ($items as $data) {
            $writer->writeRow($data);
        }
        return $writer->getContents();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _getHeaderColumns()
    {
        return array_merge($this->columnProvider->getHeaders($this->getAttributeCollection(), $this->_parameters), $this->_permanentAttributes);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function exportItem($item)
    {
        // will not implement this method as it is legacy interface
    }

    /**
     * @inheritdoc
     */
    public function getEntityTypeCode()
    {
        return 'cms_block';
    }

    /**
     * @inheritdoc
     */
    protected function _getEntityCollection()
    {
        // will not implement this method as it is legacy interface
    }
}
