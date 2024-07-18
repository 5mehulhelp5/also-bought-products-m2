<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Associate
 * @package MageMoto\AlsoBought\Model
 */
class Associate extends AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Associate::class);
    }
}
