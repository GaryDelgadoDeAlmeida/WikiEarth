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

    public function __construct()
    {
        $this->idArchive = new ArrayCollection();
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
}
