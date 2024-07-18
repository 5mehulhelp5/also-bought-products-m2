<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Console\Command;

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
