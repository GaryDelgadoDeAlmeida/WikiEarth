<?php

namespace App\Entity;

use App\Repository\ArticleElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleElementRepository::class)
 */
class ArticleElement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Article::class, mappedBy="articleElement", cascade={"persist", "remove"})
     */
    private $article;

    /**
     * @ORM\OneToOne(targetEntity=Element::class, inversedBy="articleElement", cascade={"persist", "remove"})
     */
    private $element;

    /**
     * @ORM\Column(type="json")
     */
    private $generality = [];

    /**
     * @ORM\Column(type="json")
     */
    private $description = [];

    /**
     * @ORM\Column(type="json")
     */
    private $characteristics = [];

    /**
     * @ORM\Column(type="json")
     */
    private $property = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $utilization = [];

    /**
     * @ORM\OneToMany(targetEntity=Reference::class, mappedBy="articleElement")
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=MediaGallery::class, mappedBy="articleElement")
     */
    private $mediaGalleries;

    public function __construct()
    {
        $this->reference = new ArrayCollection();
        $this->mediaGalleries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getGenerality(): ?array
    {
        return $this->generality;
    }

    public function setGenerality(array $generality): self
    {
        $this->generality = $generality;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCharacteristics(): ?array
    {
        return $this->characteristics;
    }

    public function setCharacteristics(array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function getProperty(): ?array
    {
        return $this->property;
    }

    public function setProperty(array $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getUtilization(): ?array
    {
        return $this->utilization;
    }

    public function setUtilization(?array $utilization): self
    {
        $this->utilization = $utilization;

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

    /**
     * @return Collection|Reference[]
     */
    public function getReference(): Collection
    {
        return $this->reference;
    }

    public function addReference(Reference $reference): self
    {
        if (!$this->reference->contains($reference)) {
            $this->reference[] = $reference;
            $reference->setArticleElement($this);
        }

        return $this;
    }

    public function removeReference(Reference $reference): self
    {
        if ($this->reference->removeElement($reference)) {
            // set the owning side to null (unless already changed)
            if ($reference->getArticleElement() === $this) {
                $reference->setArticleElement(null);
            }
        }

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        // unset the owning side of the relation if necessary
        if ($article === null && $this->article !== null) {
            $this->article->setArticleElement(null);
        }

        // set the owning side of the relation if necessary
        if ($article !== null && $article->getArticleElement() !== $this) {
            $article->setArticleElement($this);
        }

        $this->article = $article;

        return $this;
    }

    /**
     * @return Collection|MediaGallery[]
     */
    public function getMediaGalleries(): Collection
    {
        return $this->mediaGalleries;
    }

    public function addMediaGallery(MediaGallery $mediaGallery): self
    {
        if (!$this->mediaGalleries->contains($mediaGallery)) {
            $this->mediaGalleries[] = $mediaGallery;
            $mediaGallery->setArticleElement($this);
        }

        return $this;
    }

    public function removeMediaGallery(MediaGallery $mediaGallery): self
    {
        if ($this->mediaGalleries->removeElement($mediaGallery)) {
            // set the owning side to null (unless already changed)
            if ($mediaGallery->getArticleElement() === $this) {
                $mediaGallery->setArticleElement(null);
            }
        }

        return $this;
    }
}
