<?php

namespace App\Entity;

use App\Repository\MediaGalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MediaGalleryRepository::class)
 */
class MediaGallery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mediaType;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleLivingThing::class, inversedBy="mediaGallery")
     */
    private $articleLivingThing;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleElement::class, inversedBy="mediaGalleries")
     */
    private $articleElement;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleMineral::class, inversedBy="mediaGalleries")
     */
    private $articleMineral;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getArticleLivingThing(): ?ArticleLivingThing
    {
        return $this->articleLivingThing;
    }

    public function setArticleLivingThing(?ArticleLivingThing $articleLivingThing): self
    {
        $this->articleLivingThing = $articleLivingThing;

        return $this;
    }

    public function getArticleElement(): ?ArticleElement
    {
        return $this->articleElement;
    }

    public function setArticleElement(?ArticleElement $articleElement): self
    {
        $this->articleElement = $articleElement;

        return $this;
    }

    public function getArticleMineral(): ?ArticleMineral
    {
        return $this->articleMineral;
    }

    public function setArticleMineral(?ArticleMineral $articleMineral): self
    {
        $this->articleMineral = $articleMineral;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
