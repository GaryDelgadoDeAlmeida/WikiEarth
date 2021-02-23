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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articleElements")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Element::class, inversedBy="articleElement", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $element;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

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
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Reference::class, mappedBy="articleElement")
     */
    private $reference;

    public function __construct()
    {
        $this->reference = new ArrayCollection();
    }

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

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(Element $element): self
    {
        $this->element = $element;

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

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

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
}
