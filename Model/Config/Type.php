<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Model\Config;

use Exception;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mavenbird\AlsoBought\Model\Indexer\Action;

/**
 * Class Scope
 */
class Type extends Value
{
    /**
     * @var ManagerInterface
     */
    protected $message;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Type constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ManagerInterface $message
     * @param IndexerRegistry $indexerRegistry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ManagerInterface $message,
        IndexerRegistry $indexerRegistry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->message         = $message;
        $this->indexerRegistry = $indexerRegistry;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this|Value
     * @throws Exception
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $alsoBoughtIndexer = $this->indexerRegistry->get(Action::INDEXER_ID);
            $this->message->addNoticeMessage(__('We found updated rules that are not applied. Please run command "php bin/magento alsobought:reindex-all" to update your Who Bought This Also Bought data.'));
            $alsoBoughtIndexer->invalidate();
        }

        return $this;
    }
}
