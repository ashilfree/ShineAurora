<?php

namespace App\Entity;

use App\Repository\DiscountBannerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=DiscountBannerRepository::class)
 * @Vich\Uploadable()
 */
class DiscountBanner
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
     * @ORM\Column(type="string", length=255)
     */
    private $subTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subTitleAr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subTitle2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subTitleAr2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startingAt;

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
     * @Vich\UploadableField(mapping="banner_images", fileNameProperty="fileName")
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

        return $this;
    }

    public function getTitleAr(): ?string
    {
        return $this->titleAr;
    }

    public function setTitleAr(string $titleAr): self
    {
        $this->titleAr = $titleAr;

        return $this;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(string $subTitle): self
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    public function getSubTitleAr(): ?string
    {
        return $this->subTitleAr;
    }

    public function setSubTitleAr(string $subTitleAr): self
    {
        $this->subTitleAr = $subTitleAr;

        return $this;
    }

    public function getSubTitle2(): ?string
    {
        return $this->subTitle2;
    }

    public function setSubTitle2(string $subTitle2): self
    {
        $this->subTitle2 = $subTitle2;

        return $this;
    }

    public function getSubTitleAr2(): ?string
    {
        return $this->subTitleAr2;
    }

    public function setSubTitleAr2(string $subTitleAr2): self
    {
        $this->subTitleAr2 = $subTitleAr2;

        return $this;
    }

    public function getStartingAt(): ?\DateTimeInterface
    {
        return $this->startingAt;
    }

    public function setStartingAt(\DateTimeInterface $startingAt): self
    {
        $this->startingAt = $startingAt;

        return $this;
    }

    public function getBtnTitle(): ?string
    {
        return $this->btnTitle;
    }

    public function setBtnTitle(string $btnTitle): self
    {
        $this->btnTitle = $btnTitle;

        return $this;
    }

    public function getBtnTitleAr(): ?string
    {
        return $this->btnTitleAr;
    }

    public function setBtnTitleAr(string $btnTitleAr): self
    {
        $this->btnTitleAr = $btnTitleAr;

        return $this;
    }

    public function getBtnUrl(): ?string
    {
        return $this->btnUrl;
    }

    public function setBtnUrl(string $btnUrl): self
    {
        $this->btnUrl = $btnUrl;

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
