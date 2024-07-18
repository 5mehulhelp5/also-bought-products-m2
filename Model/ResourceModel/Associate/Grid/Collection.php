<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model\ResourceModel\Associate\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package MageMoto\AlsoBought\Model\ResourceModel\AlsoBought\Grid
 */
class Collection extends SearchResult
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mm_wbab_product_assoc',
        $resourceModel = \MageMoto\AlsoBought\Model\ResourceModel\Associate\Collection::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(
                ['ass' => $this->getMainTable()],
                'main_table.product_id = ass.product_assoc_id AND main_table.product_assoc_id = ass.product_id',
                ['ass_product_sup' => 'ass.product_sup']
            )
            ->joinLeft(
                ['cpe1' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = cpe1.entity_id',
                ['sku' => 'cpe1.sku']
            )->joinLeft(
                ['cpe2' => $this->getTable('catalog_product_entity')],
                'main_table.product_assoc_id = cpe2.entity_id',
                ['associate_sku' => 'cpe2.sku']
            );

        $this->getSelect()
            ->where('main_table.product_id < main_table.product_assoc_id');

        $this->addFilterToMap('sku', 'cpe1.sku')
            ->addFilterToMap('associate_sku', 'cpe2.sku')
            ->addFilterToMap('ass_product_sup', 'ass.product_sup')
            ->addFilterToMap('sup', 'main_table.sup')
            ->addFilterToMap('product_sup', 'main_table.product_sup');

        return $this;
    }
}
