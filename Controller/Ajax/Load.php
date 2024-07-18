<?php
/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mavenbird\AlsoBought\Controller\Ajax;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Mavenbird\AlsoBought\Block\Product\ProductList\ProductList;
use Mavenbird\AlsoBought\Helper\Data as HelperData;
use Psr\Log\LoggerInterface;

class Load extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Compare
     */
    protected $helperCompare;

    /**
     * Load constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $logger
     * @param HelperData $helper
     * @param CategoryFactory $categoryFactory
     * @param Compare $helperCompare
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $logger,
        HelperData $helper,
        CategoryFactory $categoryFactory,
        Compare $helperCompare
    ) {
        $this->logger            = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper            = $helper;
        $this->categoryFactory   = $categoryFactory;
        $this->helperCompare     = $helperCompare;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $result = ['status' => false];
        $params = $this->getRequest()->getParams();
        if (!isset($params['entity_id'], $params['action']) || !$this->helper->isEnabled()) {
            return $this->getResponse()->representJson(HelperData::jsonEncode($result));
        }

        try {
            if ($params['action'] === 'catalog_category_view') {
                /** @var Category $category */
                $category   = $this->categoryFactory->create()->load($params['entity_id']);
                $productIds = $category->getProductCollection()->getAllIds();
            } else {
                $productIds = $params['entity_id'];
            }

            if (!empty($productIds)) {
                $data = [];
                /** @var ProductList $block */
                $block      = $this->resultPageFactory->create()->getLayout()
                    ->createBlock(ProductList::class)
                    ->setProductIds($productIds)
                    ->setAction($params['action']);
                $collection = $block->getProductCollection();
                if (!empty($collection)) {
                    foreach ($collection as $product) {
                        $data[$product->getId()] = $this->processProductData($block, $product);
                    }
                }

                if (!empty($data)) {
                    $result = [
                        'status'      => true,
                        'productList' => $data
                    ];
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e);

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        return $this->getResponse()->representJson(HelperData::jsonEncode($result));
    }

    /**
     * @param ProductList $block
     * @param Product $product
     *
     * @return array
     */
    protected function processProductData($block, $product)
    {
        $productId = $product->getId();

        return [
            'product_name'        => $product->getName(),
            'product_id'          => $productId,
            'product_sku'         => $product->getSku(),
            'product_url'         => $block->getProductUrl($product),
            'product_image'       => $block->getImage(
                $product,
                'recently_viewed_products_grid_content_widget'
            )->toHtml(),
            'product_small_image' => $block->getImage($product, 'product_thumbnail_image')->toHtml(),
            'product_review'      => $block->getReviewsSummaryHtml(
                $product,
                ReviewRendererInterface::SHORT_VIEW,
                true
            ),
            'product_price'       => $block->getProductPrice($product),
            'post_params'         => $block->getAddToCartPostParams($product),
            'wishlist_params'     => $block->getPostData($block->getAddToWishlistParams($product)),
            'compare_params'      => $block->getPostData($this->helperCompare->getPostDataParams($product)),
            'isSaleable'          => $product->isSalable(),
            'stock'               => $product->getIsSalable()
        ];
    }
}
