<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Console\Command;

/**
 * Class ReindexAll
 * @package MageMoto\AlsoBought\Console\Command
 */
class ReindexAll extends Reindex
{
    /**
     * @var bool
     */
    protected $indexAll = true;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('alsobought:reindex-all')
            ->setDescription('Reindex All Who Bought This Item Also Bought');
    }
}
