<?php
namespace Texboy\CmsImportExport\Model\Import\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\Behavior\Basic;

class Custom extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior {
    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'custom_cms';
    }

    /**
     * @inheritdoc
     */
    public function getNotes($entityCode)
    {
        $messages = ['catalog_product' => [
            Import::BEHAVIOR_APPEND => __(
                "New cms block data is added to the existing data for the existing entries in the database. "
                . "All fields except block_id can be updated."
            )
        ]];
        return isset($messages[$entityCode]) ? $messages[$entityCode] : [];
    }
}
