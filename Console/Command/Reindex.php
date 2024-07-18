<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Console\Command;

use Exception;
use Magento\Framework\App\State;
use MageMoto\AlsoBought\Model\ResourceModel\AssociateFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Reindex
 * @package MageMoto\AlsoBought\Console\Command
 */
class Reindex extends Command
{
    /**
     * @var bool
     */
    protected $indexAll = false;

    /**
     * @var AssociateFactory
     */
    protected $associateFactory;

    /**
     * @var State
     */
    protected $appState;

    /**
     * Reindex constructor.
     *
     * @param AssociateFactory $associateFactory
     * @param State $appState
     * @param null $name
     */
    public function __construct(
        AssociateFactory $associateFactory,
        State $appState,
        $name = null
    ) {
        $this->associateFactory = $associateFactory;
        $this->appState         = $appState;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('alsobought:reindex')
            ->setDescription('Reindex Who Bought This Item Also Bought');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('frontend');
            $this->associateFactory->create()->reIndex($this->indexAll);
            $output->writeln('<info>Successfully!</info>');
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}
