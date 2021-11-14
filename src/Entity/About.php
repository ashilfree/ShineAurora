<?php

namespace App\Entity;

use App\Repository\AboutRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AboutRepository::class)
 * @Vich\Uploadable()
 */
class About
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleAr;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionAr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $btnTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $btnTitleAr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $btnUrl;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="about_images", fileNameProperty="fileName")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        if (null !== $title)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        if (null !== $description)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBtnTitle(): ?string
    {
        return $this->btnTitle;
    }

    public function setBtnTitle(string $btnTitle): self
    {
        $this->btnTitle = $btnTitle;
        if (null !== $btnTitle)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBtnTitleAr(): ?string
    {
        return $this->btnTitleAr;
    }

    public function setBtnTitleAr(string $btnTitleAr): self
    {
        $this->btnTitleAr = $btnTitleAr;
        if (null !== $btnTitleAr)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBtnUrl(): ?string
    {
        return $this->btnUrl;
    }

    public function setBtnUrl(string $btnUrl): self
    {
        $this->btnUrl = $btnUrl;
        if (null !== $btnUrl)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getTitleAr(): ?string
    {
        return $this->titleAr;
    }

    public function setTitleAr(string $titleAr): self
    {
        $this->titleAr = $titleAr;
        if (null !== $titleAr)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDescriptionAr(): ?string
    {
        return $this->descriptionAr;
    }

    public function setDescriptionAr(?string $descriptionAr): self
    {
        $this->descriptionAr = $descriptionAr;
        if (null !== $descriptionAr)
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
