<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use MageMoto\AlsoBought\Helper\Data;

/**
 * Class UpgradeSchema
 * @package MageMoto\AlsoBought\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * UpgradeSchema constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($this->helper->versionCompare('2.3.0')) {
            return $this;
        }

        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $table = $connection->newTable($installer->getTable('mm_wbab_product_assoc'))
                ->addColumn('assoc_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Associate ID')
                ->addColumn('product_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'nullable' => false,
                ], 'Product ID')
                ->addColumn('product_assoc_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'nullable' => false,
                ], 'Product Associated ID')
                ->addColumn(
                    'product_sup',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Total Bought Product'
                )
                ->addColumn(
                    'sup',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Total Bought Together'
                )
                ->addColumn('rpf', Table::TYPE_DECIMAL, '12,4', ['default' => '0'], 'Rule Power Factor')
                ->addIndex($installer->getIdxName('mm_wbab_product_assoc', ['rpf']), ['rpf'])
                ->addIndex(
                    $installer->getIdxName('mm_wbab_product_assoc', ['product_id', 'product_assoc_id']),
                    ['product_id', 'product_assoc_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mm_wbab_product_assoc',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'product_assoc_id',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_assoc_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->dropColumn($installer->getTable('sales_order'), 'magemoto_alsobought_status');
            $installer->getConnection()->dropTable($installer->getTable('magemoto_wbab_product_link'));
        }

        $installer->endSetup();
    }
}
