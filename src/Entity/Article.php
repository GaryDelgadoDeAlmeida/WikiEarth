<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $idUser;

    /**
     * @ORM\OneToMany(targetEntity=Archive::class, mappedBy="article")
     */
    private $idArchive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=ArticleContent::class, mappedBy="article")
     */
    private $articleContent;

    /**
     * @ORM\OneToMany(targetEntity=SourceLink::class, mappedBy="idArticle")
     */
    private $sourceLinks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->idArchive = new ArrayCollection();
        $this->sourceLinks = new ArrayCollection();
        $this->articleContent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * @return Collection|Archive[]
     */
    public function getIdArchive(): Collection
    {
        return $this->idArchive;
    }

    public function addIdArchive(Archive $idArchive): self
    {
        if (!$this->idArchive->contains($idArchive)) {
            $this->idArchive[] = $idArchive;
            $idArchive->setArticle($this);
        }

        return $this;
    }

    public function removeIdArchive(Archive $idArchive): self
    {
        if ($this->idArchive->contains($idArchive)) {
            $this->idArchive->removeElement($idArchive);
            // set the owning side to null (unless already changed)
            if ($idArchive->getArticle() === $this) {
                $idArchive->setArticle(null);
            }
        }

        return $this;
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

    /**
     * @return Collection|ArticleContent[]
     */
    public function getArticleContent(): Collection
    {
        return $this->articleContent;
    }

    public function addArticleContent(ArticleContent $articleContent): self
    {
        if (!$this->articleContent->contains($articleContent)) {
            $this->articleContent[] = $articleContent;
            $articleContent->setArticle($this);
        }

        return $this;
    }

    public function removeArticleContent(ArticleContent $articleContent): self
    {
        if ($this->articleContent->contains($articleContent)) {
            $this->articleContent->removeElement($articleContent);
            // set the owning side to null (unless already changed)
            if ($articleContent->getArticle() === $this) {
                $articleContent->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SourceLink[]
     */
    public function getSourceLinks(): Collection
    {
        return $this->sourceLinks;
    }

    public function addSourceLink(SourceLink $sourceLink): self
    {
        if (!$this->sourceLinks->contains($sourceLink)) {
            $this->sourceLinks[] = $sourceLink;
            $sourceLink->setIdArticle($this);
        }

        return $this;
    }

    public function removeSourceLink(SourceLink $sourceLink): self
    {
        if ($this->sourceLinks->contains($sourceLink)) {
            $this->sourceLinks->removeElement($sourceLink);
            // set the owning side to null (unless already changed)
            if ($sourceLink->getIdArticle() === $this) {
                $sourceLink->setIdArticle(null);
            }
        }

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
