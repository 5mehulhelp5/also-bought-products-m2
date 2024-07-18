<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mview\ActionInterface;
use Mavenbird\AlsoBought\Helper\Data;
use Mavenbird\AlsoBought\Model\ResourceModel\Associate;
use Mavenbird\AlsoBought\Model\ResourceModel\AssociateFactory;
use Psr\Log\LoggerInterface;

class Action implements \Magento\Framework\Indexer\ActionInterface, ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'mm_also_bought';

    /**
     * @var Associate
     */
    protected $associateResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Action constructor.
     *
     * @param AssociateFactory $associateResourceFactory
     * @param Data $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(
        AssociateFactory $associateResourceFactory,
        Data $helperData,
        LoggerInterface $logger
    ) {
        $this->associateResource = $associateResourceFactory->create();
        $this->helperData        = $helperData;
        $this->logger            = $logger;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     * @throws LocalizedException
     */
    public function executeFull()
    {
        $this->associateResource->reIndex(true);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     *
     * @return void
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @return void
     * @throws LocalizedException
     */
    public function executeRow($id)
    {
        if (empty($id) || $id > $this->helperData->getCurrentOrderId()) {
            $this->associateResource->reIndex();
        } else {
            $this->executeAction([$id]);
        }
    }

    /**
     * Execute action for single entity or list of entities
     *
     * @param int[] $ids
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function executeAction($ids)
    {
        $ids = array_unique($ids);
        $this->associateResource->reIndex(false, $ids);

        return $this;
    }
}
