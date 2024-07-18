<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ProductsRepositoryInterface
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
