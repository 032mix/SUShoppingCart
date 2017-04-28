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

    private $allPromotions;

    private $biggestPromoGlobal;

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

        /* Same for the global promotion */
        if ($this->biggestPromoGlobal === null) {
            $this->biggestPromoGlobal = $promotionRepo->getBiggestActiveGlobalPromotion();
        }

        $category = $product->getProductcategory();
        $categoryId = $category->getId();
        $productId = $product->getId();
        $promotion = 0;

        foreach ($this->allPromotions as $promotionToCheck) {
            $promotionCategory = $promotionToCheck->getCategory();
            $promotionProduct = $promotionToCheck->getProduct();

            if ($promotionCategory == $category) {
                $biggestPromoCategory = $promotionRepo->getBiggestActiveCategoryPromotion($categoryId);
                if ($promotion < $biggestPromoCategory) {
                    $promotion = $biggestPromoCategory;
                }
            } elseif ($promotionProduct == $product) {
                $biggestPromoProduct = $promotionRepo->getBiggestActiveProductPromotion($productId);
                if ($promotion < $biggestPromoProduct) {
                    $promotion = $biggestPromoProduct;
                }
            } else {
                if ($promotion < $this->biggestPromoGlobal) {
                    $promotion = $this->biggestPromoGlobal;
                }
            }
        }

        $price = $product->getPrice();

        $promotedPrice = $price - $price * ($promotion / 100);


        return $promotedPrice;
    }
}
