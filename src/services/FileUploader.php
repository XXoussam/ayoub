<?php

namespace App\services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
        * @var ContainerInterface
     */
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function uploadFile(UploadedFile $file): string
    {
        $filename = md5(uniqid()).'.'.$file->guessClientExtension();
        $file->move(
        // TODO : get file directory
            $this->container->getParameter('uploads_dir'),
            $filename
        );

        return $filename;
    }



}