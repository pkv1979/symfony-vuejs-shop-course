<?php

namespace App\Utils\File;

use App\Utils\Filesystem\FilesystemWorker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
  /**
   * @var SluggerInterface
   */
  private $slugger;
  /**
   * @var string
   */
  private $uploadsTempDir;
  /**
   * @var FileSystemWorker
   */
  private $filesystemWorker;

  public function __construct(SluggerInterface $slugger, FilesystemWorker $filesystemWorker, string $uploadsTempDir)
  {
    $this->slugger = $slugger;
    $this->filesystemWorker = $filesystemWorker;
    $this->uploadsTempDir = $uploadsTempDir;
  }

  /**
   * @param UploadedFile $uploadedFile
   * @return string|null
   */
  public function saveUploaddedFileIntoTemp(UploadedFile $uploadedFile)
  {
    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);

    $filename = sprintf('%s-%s.%s', $safeFilename, uniqid(), $uploadedFile->guessExtension());

    $this->filesystemWorker->createFolderIfNotExists($this->uploadsTempDir);

    try {
      $uploadedFile->move($this->uploadsTempDir, $filename);
    } catch (FileException $exception) {
      return null;
    }

    return $filename;
  }
}