<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=ArticleLivingThing::class, inversedBy="article", cascade={"persist", "remove"})
     */
    private $articleLivingThing;

    /**
     * @ORM\OneToOne(targetEntity=ArticleElement::class, inversedBy="article", cascade={"persist", "remove"})
     */
    private $articleElement;

    /**
     * @ORM\OneToOne(targetEntity=ArticleMineral::class, inversedBy="article", cascade={"persist", "remove"})
     */
    private $articleMineral;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "The title of this LivingThing must be at least {{ limit }} characters long",
     *      maxMessage = "Your title of this LivingThing cannot be higher than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
