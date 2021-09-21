<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\Indexer\Controller\Adminhtml\Indexer;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Indexer\Controller\Adminhtml\Indexer as Action;
use Psr\Log\LoggerInterface;

/**
 * Mass reindex controller
 */
class MassReindex extends Action
{
    /**
     * Indexer registry
     *
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param IndexerRegistry $indexerRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        IndexerRegistry $indexerRegistry,
        LoggerInterface $logger
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->logger = $logger;

        parent::__construct(
            $context
        );
    }

    /**
     * Run selected indexers
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(
                __('Please select indexers.')
            );
            return $this->_redirect('*/*/list');
        }

        try {
            foreach ($indexerIds as $indexerId) {
                /** @var IndexerInterface $indexer */
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll();
            }

            $this->messageManager->addSuccess(
                __('Total of %1 index(es) have reindexed data.', count($indexerIds))
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('There was a problem with reindexing process.')
            );
            $this->logger->critical($e);
        }
        return $this->_redirect('*/*/list');
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Magento_Indexer::reindex'
        );
    }
}
