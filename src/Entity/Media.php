<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Gedmo\Uploadable(['allowOverwrite' => true])]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $realName = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Gedmo\UploadableFileMimeType]
    private $extension;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    #[Gedmo\UploadableFileMimeType]
    private $mimeType;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 2, nullable: true)]
    #[Gedmo\UploadableFileSize]
    private $size;

    #[ORM\Column(type: 'string', length: 300, nullable: true)]
    #[Gedmo\UploadableFilePath]
    private $path;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Event $event = null;

    public function __toString(): string
    {
        return $this->realName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealName(): ?string
    {
        return $this->realName;
    }

    public function setRealName(string $realName): self
    {
        $this->realName = $realName;

        return $this;
    }


    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtension(): mixed
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension(mixed $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return mixed
     */
    public function getMimeType(): mixed
    {
        return $this->mimeType;
    }

    /**
     * @param mixed $mimeType
     */
    public function setMimeType(mixed $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return mixed
     */
    public function getSize(): mixed
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize(mixed $size): void
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getPath(): mixed
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath(mixed $path): void
    {
        $this->path = $path;
    }
}
