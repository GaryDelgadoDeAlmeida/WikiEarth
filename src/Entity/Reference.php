<?php

namespace App\Entity;

use App\Repository\ReferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReferenceRepository::class)
 */
class Reference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleLivingThing::class, inversedBy="reference")
     */
    private $articleLivingThing;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleElement::class, inversedBy="reference")
     */
    private $articleElement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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
