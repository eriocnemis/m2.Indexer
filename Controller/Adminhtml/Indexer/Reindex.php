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
 * Reindex controller
 */
class Reindex extends Action
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
     * Run selected indexer
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $indexerId = $this->getRequest()->getParam('id');
        if (!$indexerId) {
            $this->messageManager->addError(
                __('Please select indexer.')
            );
        } else {
            try {
                /** @var IndexerInterface $indexer */
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll();

                $this->messageManager->addSuccess(
                    __('%1 index was rebuilt.', $indexer->getTitle())
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('There was a problem with reindexing process.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/list');
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
