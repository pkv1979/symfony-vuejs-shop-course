<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
  /**
   * @var ProductImageManager
   */
  private $productImageManager;

  /**
   * @var string
   */
  private $productImagesDir;

  public function __construct(EntityManagerInterface $entityManager, ProductImageManager $productImageManager, string $productImagesDir) 
  {
    parent::__construct($entityManager);
    
    $this->productImageManager = $productImageManager;
    $this->productImagesDir = $productImagesDir;
  }

  /**
   * @return ObjectRepository
   */
  public function getRepository(): ObjectRepository
  {
    return $this->entityManager->getRepository(Product::class);
  }

  /**
   * @param object $product
   */
  public function remove(object $product)
  {
    $product->setIsDeleted(true);
    $this->save($product);
  }

  /**
   * @param Product $product
   * @return string
   */
  public function getProductImagesDir(Product $product)
  {
    return sprintf('%s/%s', $this->productImagesDir, $product->getId());
  }

  public function updateProductImages(Product $product, string $tempImageFilename = null): Product
  {
    if (!$tempImageFilename) {
      return $product;
    }

    $productDir = $this->getProductImagesDir($product);

    $productImge = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
    $productImge->setProduct($product);
    $product->addProductImage($productImge);

    return $product;
  }
}