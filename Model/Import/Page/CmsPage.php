<?php

namespace Texboy\CmsImportExport\Model\Import\Page;

use Exception;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;


class CmsPage extends AbstractEntity
{
    const ENTITY_CODE = 'cms_page';
    const TABLE = 'cms_page';
    const ENTITY_ID_COLUMN = 'page_id';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'page_id',
        'identifier',
        'title',
        'page_layout',
        'meta_keywords',
        'meta_description',
        'content_heading',
        'content',
        'sort_order',
        'layout_update_xml',
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
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * Courses constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param PageRepository $pageRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        PageRepository $pageRepository,
        PageFactory $pageFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
        $this->initMessageTemplates();
    }

    /**
     * @inheritDoc
     * @throws CouldNotSaveException
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
                $this->savePage($row);
            }
        }
    }

    /**
     * @throws CouldNotSaveException
     */
    private function savePage(array $data) {
        $identifier = $data['identifier'];
        /** @var Page $page */
        try {
            $page = $this->pageRepository->getById($identifier);
        } catch (Exception $e) {
            // If not exist, create a new page
            $page = $this->pageFactory->create();
            $page->setIdentifier($identifier);
        }

        $page->setTitle($data['title'] ?? '');
        $page->setContent($data['content'] ?? '');
        $page->setIsActive($data['is_active'] ?? 0);
        $page->setPageLayout($data['page_layout'] ?? '');
        $page->setMetaKeywords($data['meta_keywords'] ?? '');
        $page->setMetaDescription($data['meta_description'] ?? '');
        $page->setContentHeading($data['content_heading'] ?? '');
        $page->setSortOrder($data['sort_order'] ?? '');
        $page->setLayoutUpdateXml($data['layout_update_xml'] ?? '');
        $page->setStoreId($this->jsonHelper->jsonDecode($data['store_ids']));
        $page->setStores($this->jsonHelper->jsonDecode($data['store_ids']));
        $this->pageRepository->save($page);
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
        $identifier = $rowData[PageInterface::IDENTIFIER] ?? '';
        $title = $rowData[PageInterface::TITLE] ?? '';
        $content = $rowData[PageInterface::CONTENT] ?? '';
        $storeIds = $rowData['store_ids'] ? $this->jsonHelper->jsonDecode($rowData['store_ids']) : '';
        $isActive = $rowData[PageInterface::IS_ACTIVE] ?? '';

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
