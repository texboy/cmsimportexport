<?php

namespace Texboy\CmsImportExport\Model\Import\Block;

use Exception;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;


class CmsBlock extends AbstractEntity
{
    const ENTITY_CODE = 'cms_block';
    const TABLE = 'cms_block';
    const ENTITY_ID_COLUMN = 'block_id';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        'block_id'
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'block_id',
        'title',
        'identifier',
        'content',
        'is_active',
        'store_ids'
    ];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var BlockRepository
     */
    private BlockRepository $blockRepository;

    /**
     * @var BlockFactory
     */
    private BlockFactory $blockFactory;

    /**
     * Courses constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param BlockRepository $blockRepository
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        BlockRepository $blockRepository,
        BlockFactory $blockFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
        $this->initMessageTemplates();
    }

    /**
     * @inheritDoc
     */
    protected function _importData()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $this->saveBlock($row);
            }
        }
    }
    private function saveBlock(array $data) {
        $identifier = $data['identifier'];
        /** @var Block $block */
        try {
            $block = $this->blockRepository->getById($identifier);
        } catch (Exception $e) {
            // If not exist, create a new block
            $block = $this->blockFactory->create();
            $block->setIdentifier($identifier);
        }

        $block->setTitle($data['title']);
        $block->setContent($data['content']);
        $block->setIsActive($data['is_active']);
        $block->setStoreId($this->jsonHelper->jsonDecode($data['store_ids']));
        $block->setStores($this->jsonHelper->jsonDecode($data['store_ids']));

        $this->blockRepository->save($block);
    }
    /**
     * @inheritDoc
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $identifier = $rowData[BlockInterface::IDENTIFIER] ?? '';
        $title = $rowData[BlockInterface::TITLE] ?? '';
        $content = $rowData[BlockInterface::CONTENT] ?? '';
        $storeIds = $rowData['store_ids'] ? $this->jsonHelper->jsonDecode($rowData['store_ids']) : '';
        $isActive = $rowData[BlockInterface::IS_ACTIVE] ?? '';

        if (!$identifier) {
            $this->addRowError('IdentifierIsRequired', $rowNum);
        }

        if (!$title) {
            $this->addRowError('TitleIsRequired', $rowNum);
        }
        if (!$content) {
            $this->addRowError('ContentIsRequired', $rowNum);
        }

        if (!$storeIds) {
            $this->addRowError('StoreIdsIsRequired', $rowNum);
        }

        if (!$isActive) {
            $this->addRowError('IsActiveIsRequired', $rowNum);
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Init Error Messages
     */
    private function initMessageTemplates()
    {
        $this->addMessageTemplate(
            'IdentifierIsRequired',
            __('Identifier cannot be empty.')
        );
        $this->addMessageTemplate(
            'TitleIsRequired',
            __('Title cannot be empty.')
        );
        $this->addMessageTemplate(
            'ContentIsRequired',
            __('Content cannot be empty.')
        );
        $this->addMessageTemplate(
            'StoreIdsIsRequired',
            __('Store ids cannot be empty.')
        );
        $this->addMessageTemplate(
            'IsActiveIsRequired',
            __('IsActive cannot be empty.')
        );
    }
}
