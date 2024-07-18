<?php
/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MageMoto\AlsoBought\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ProductsRepositoryInterface
 * @package MageMoto\AlsoBought\Api
 */
interface AlsoBoughtRepositoryInterface
{
    /**
     * @param string $sku
     *
     * @return ProductInterface[]
     * @throws NoSuchEntityException
     */
    public function getList($sku);
}
