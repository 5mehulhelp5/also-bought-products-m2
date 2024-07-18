<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Mavenbird\AlsoBought\Helper\Data;
use Mavenbird\AlsoBought\Model\Indexer\Action;

class SaveOrderItemObserver implements ObserverInterface
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * SaveOrderItemObserver constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     * @param Data $helperData
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        Data $helperData
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->helperData      = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if (!$order->getId()) {
            //order not saved in the database
            return $this;
        }

        $processOrderStatus = $this->helperData->getProcessOrderStatus();
        if (empty($processOrderStatus) || !in_array($order->getStatus(), $processOrderStatus, true)) {
            return $this;
        }

        $alsoBoughtIndexer = $this->indexerRegistry->get(Action::INDEXER_ID);
        if ($alsoBoughtIndexer->isScheduled()) {
            $alsoBoughtIndexer->invalidate();
        } else {
            $alsoBoughtIndexer->reindexRow($order->getId());
        }

        return $this;
    }
}
