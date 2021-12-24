<?php
declare(strict_types=1);

namespace Texboy\CmsImportExport\Model\Export\Page;

use Exception;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use \Magento\Cms\Model\ResourceModel\Page\Collection as EntityCollection;
use Magento\InventoryImportExport\Model\Export\ColumnProviderInterface;
use Texboy\CmsImportExport\Model\Export\Page\PageCollectionFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @inheritdoc
 */
class CmsPage extends AbstractEntity
{
    /**
     * @var string[]
     */
    protected $_permanentAttributes = [PageStoreModifier::STORE_ID_COLUMN];

    /**
     * @var AttributeCollectionProvider
     */
    private $attributeCollectionProvider;

    /**
     * @var PageCollectionFactoryInterface
     */
    private $entityCollectionFactory;

    /**
     * @var ColumnProviderInterface
     */
    private $columnProvider;

    /**
     * @var PageStoreModifier
     */
    private $pageStoreModifier;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param PageCollectionFactoryInterface $entityCollectionFactory
     * @param ColumnProviderInterface $columnProvider
     * @paarm PageStoreModifier $pageStoreModifier
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface             $scopeConfig,
        StoreManagerInterface            $storeManager,
        ExportFactory                    $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        AttributeCollectionProvider      $attributeCollectionProvider,
        PageCollectionFactoryInterface  $entityCollectionFactory,
        ColumnProviderInterface          $columnProvider,
        PageStoreModifier               $pageStoreModifier,
        array                            $data = []
    ) {
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->columnProvider = $columnProvider;
        $this->pageStoreModifier = $pageStoreModifier;
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
        $items = $this->pageStoreModifier->addStoreIdToPages($collection->getItems());
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
        return 'cms_page';
    }

    /**
     * @inheritdoc
     */
    protected function _getEntityCollection()
    {
        // will not implement this method as it is legacy interface
    }
}
