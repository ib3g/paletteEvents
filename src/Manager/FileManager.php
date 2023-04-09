<?php

/*
 * This file is part of the Kuraq project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Hello Pomelo <contact@hello-pomelo.com>
 *
 */

namespace App\Manager;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    /** @var UploadableManager */
    private $uploadableManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FileManager constructor.
     */
    public function __construct(UploadableManager $uploadableManager, EntityManagerInterface $em)
    {
        $this->uploadableManager = $uploadableManager;
        $this->em = $em;
    }

    /**
     * @return Media
     */
    public function uploadFile(UploadedFile $uploadedFile, $andFlush = true)
    {
        $file = new Media();
        $file->setRealName($uploadedFile->getClientOriginalName());

        $this->uploadableManager->markEntityToUpload($file, $uploadedFile);

        if ($andFlush) {
            $this->em->persist($file);
            $this->em->flush();
        }

        return $file;
    }
}
