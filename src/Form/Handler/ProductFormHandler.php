<?php

namespace App\Form\Handler;

use App\Entity\Product;
use Symfony\Component\Form\Form;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;

class ProductFormHandler
{
  /**
   * @var ProductManager
   */
  private $productManager;

  /**
   * @var FileSaver
   */
  private $fileSaver;
  
  public function __construct(ProductManager $productManager, FileSaver $fileSaver)
  {
    $this->productManager = $productManager;
    $this->fileSaver = $fileSaver;
  }

  public function processEditForm(Product $product, Form $form)
  {
    $this->productManager->save($product);

    $newImageFile = $form->get('newImage')->getData();

    $tempImageFilename = $newImageFile
      ? $this->fileSaver->saveUploaddedFileIntoTemp($newImageFile)
      : null;
    
    $this->productManager->updateProductImages($product, $tempImageFilename);
    
    $this->productManager->save($product);

    return $product;
  }
}