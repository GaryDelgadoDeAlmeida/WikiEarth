<?php

namespace App\Entity;

use App\Repository\ArticleMineralRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleMineralRepository::class)
 */
class ArticleMineral
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Article::class, mappedBy="articleMineral", cascade={"persist", "remove"})
     */
    private $article;

    /**
     * @ORM\OneToOne(targetEntity=Mineral::class, inversedBy="articleMineral", cascade={"persist", "remove"})
     */
    private $mineral;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $generality = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $etymology = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $properties = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $geology = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $mining = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        // unset the owning side of the relation if necessary
        if ($article === null && $this->article !== null) {
            $this->article->setMineral(null);
        }

        // set the owning side of the relation if necessary
        if ($article !== null && $article->getMineral() !== $this) {
            $article->setMineral($this);
        }

        $this->article = $article;

        return $this;
    }

    public function getMineral(): ?Mineral
    {
        return $this->mineral;
    }

    public function setMineral(?Mineral $mineral): self
    {
        $this->mineral = $mineral;

        return $this;
    }

    public function getGenerality(): ?array
    {
        return $this->generality;
    }

    public function setGenerality(?array $generality): self
    {
        $this->generality = $generality;

        return $this;
    }

    public function getEtymology(): ?array
    {
        return $this->etymology;
    }

    public function setEtymology(?array $etymology): self
    {
        $this->etymology = $etymology;

        return $this;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function setProperties(?array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getGeology(): ?array
    {
        return $this->geology;
    }

    public function setGeology(?array $geology): self
    {
        $this->geology = $geology;

        return $this;
    }

    public function getMining(): ?array
    {
        return $this->mining;
    }

    public function setMining(?array $mining): self
    {
        $this->mining = $mining;

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
