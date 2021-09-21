<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\Indexer\Block\Adminhtml\Grid\Column;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Action render
 *
 * @api
 */
class Reindex extends Column
{
    /**
     * Add decorated action to column
     *
     * @return mixed[]
     */
    public function getFrameCallback()
    {
        return [$this, 'decorateAction'];
    }

    /**
     * Decorate values to column
     *
     * @param string $value
     * @param DataObject $row
     * @param Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateAction($value, $row, $column, $isExport)
    {
        $url = $this->getUrl('*/indexer/reindex', ['id' => $row->getData('indexer_id')]);
        return $isExport
            ? $value
            : '<a href="' . $url .
            '" title="' . __('Forced rebuilding of the %1 index.', $row->getData('title')) .
            '">' . __('Reindex') . '</a>';
    }
}
