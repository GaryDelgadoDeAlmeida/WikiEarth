<?php

namespace App\Entity;

use App\Repository\ArchiveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArchiveRepository::class)
 */
class Archive
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=MediaGallery::class, mappedBy="archive")
     */
    private $mediaGallery;

    /**
     * @ORM\Column(type="integer")
     */
    private $idTemplate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $concernedTable;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="idArchive")
     */
    private $article;

    public function __construct()
    {
        $this->mediaGallery = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|MediaGallery[]
     */
    public function getMediaGallery(): Collection
    {
        return $this->mediaGallery;
    }

    public function addMediaGallery(MediaGallery $mediaGallery): self
    {
        if (!$this->mediaGallery->contains($mediaGallery)) {
            $this->mediaGallery[] = $mediaGallery;
            $mediaGallery->setArchive($this);
        }

        return $this;
    }

    public function removeMediaGallery(MediaGallery $mediaGallery): self
    {
        if ($this->mediaGallery->contains($mediaGallery)) {
            $this->mediaGallery->removeElement($mediaGallery);
            // set the owning side to null (unless already changed)
            if ($mediaGallery->getArchive() === $this) {
                $mediaGallery->setArchive(null);
            }
        }

        return $this;
    }

    public function getIdTemplate(): ?int
    {
        return $this->idTemplate;
    }

    public function setIdTemplate(int $idTemplate): self
    {
        $this->idTemplate = $idTemplate;

        return $this;
    }

    public function getConcernedTable(): ?string
    {
        return $this->concernedTable;
    }

    public function setConcernedTable(string $concernedTable): self
    {
        $this->concernedTable = $concernedTable;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
