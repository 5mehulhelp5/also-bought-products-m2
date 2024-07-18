<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Model;

use Magento\Framework\Model\AbstractModel;

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
