<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
class FileUploader
{
    private $imageDirectory;
    private $musicDirectory;
    private $slugger;
    public function __construct($imageDirectory, $musicDirectory, SluggerInterface $slugger)
    {
        $this->imageDirectory = $imageDirectory;
        $this->musicDirectory = $musicDirectory;
        $this->slugger = $slugger;
    }
    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();


        if ($file->guessExtension() == 'jpg' || $file->guessExtension() == 'jpeg' || $file->guessExtension() == 'png') {
            try {
                $file->move($this->getImageDirectory(), $fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                return null; // for example
            }

        } else {
            try {
                $file->move($this->getMusicDirectory(), $fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                return null; // for example
            }
            
        }

        return $fileName;

    }
    public function getImageDirectory()
    {
        return $this->imageDirectory;
    }

    public function getMusicDirectory()
    {
        return $this->musicDirectory;
    }
}