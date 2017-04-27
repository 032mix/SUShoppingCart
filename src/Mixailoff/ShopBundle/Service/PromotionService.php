<?php

namespace Mixailoff\ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use Mixailoff\ShopBundle\Entity\Product;

class PromotionService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected $allPromotions;

    /**
     * @param Product $product
     * @return int
     */
    public function calculatePromotedPrice(Product $product)
    {
        $promotionRepo = $this->em->getRepository('MixSBundle:Promotion');

        /* Sets allPromotions if its not set already
           so that it will do it only once per page
           and not per product. */
        if (!$this->allPromotions) {
            $this->allPromotions = $promotionRepo->findAll();
        }

        $category = $product->getProductcategory();
        $categoryId = $category->getId();
        $productId = $product->getId();
        $promotion = 0;

        foreach ($this->allPromotions as $promotionToCheck) {
            $promotionCategory = $promotionToCheck->getCategory();
            $promotionProduct = $promotionToCheck->getProduct();

            if ($promotionCategory == $category) {
                if ($promotion < $biggestPromoCategory = $promotionRepo->getBiggestActiveCategoryPromotion($categoryId)) {
                    $promotion = $biggestPromoCategory;
                }
            } elseif ($promotionProduct == $product) {
                if ($promotion < $biggestPromoProduct = $promotionRepo->getBiggestActiveProductPromotion($productId)) {
                    $promotion = $biggestPromoProduct;
                }
            } elseif (!$promotionCategory && !$promotionProduct) {
                if ($promotion < $biggestPromoGlobal = $promotionRepo->getBiggestActiveGlobalPromotion()) {
                    $promotion = $biggestPromoGlobal;
                }
            }
        }

        $price = $product->getPrice();

        $promotedPrice = $price - $price * ($promotion / 100);


        return $promotedPrice;
    }
}
