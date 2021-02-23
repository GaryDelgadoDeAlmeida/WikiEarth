<?php

namespace App\Entity;

use App\Repository\ArticleLivingThingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleLivingThingRepository::class)
 */
class ArticleLivingThing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articleLivingThings")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=LivingThing::class, inversedBy="articleLivingThing", cascade={"persist", "remove"})
     */
    private $idLivingThing;

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
     * @ORM\Column(type="json", nullable=true)
     */
    private $geography = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $ecology = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $behaviour = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $wayOfLife = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $description = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $otherData = [];

    /**
     * @ORM\OneToMany(targetEntity=MediaGallery::class, mappedBy="articleLivingThing")
     */
    private $mediaGallery;

    /**
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Reference::class, mappedBy="articleLivingThing")
     */
    private $reference;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->mediaGallery = new ArrayCollection();
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

    public function getIdLivingThing(): ?LivingThing
    {
        return $this->idLivingThing;
    }

    public function setIdLivingThing(?LivingThing $idLivingThing): self
    {
        $this->idLivingThing = $idLivingThing;

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

    public function getGeography(): ?array
    {
        return $this->geography;
    }

    public function setGeography(?array $geography): self
    {
        $this->geography = $geography;

        return $this;
    }

    public function getEcology(): ?array
    {
        return $this->ecology;
    }

    public function setEcology(?array $ecology): self
    {
        $this->ecology = $ecology;

        return $this;
    }

    public function getBehaviour(): ?array
    {
        return $this->behaviour;
    }

    public function setBehaviour(?array $behaviour): self
    {
        $this->behaviour = $behaviour;

        return $this;
    }

    public function getWayOfLife(): ?array
    {
        return $this->wayOfLife;
    }

    public function setWayOfLife(?array $wayOfLife): self
    {
        $this->wayOfLife = $wayOfLife;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(?array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOtherData(): ?array
    {
        return $this->otherData;
    }

    public function setOtherData(?array $otherData): self
    {
        $this->otherData = $otherData;

        return $this;
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
            $mediaGallery->setArticleLivingThing($this);
        }

        return $this;
    }

    public function removeMediaGallery(MediaGallery $mediaGallery): self
    {
        if ($this->mediaGallery->contains($mediaGallery)) {
            $this->mediaGallery->removeElement($mediaGallery);
            // set the owning side to null (unless already changed)
            if ($mediaGallery->getArticleLivingThing() === $this) {
                $mediaGallery->setArticleLivingThing(null);
            }
        }

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
            $reference->setArticleLivingThing($this);
        }

        return $this;
    }

    public function removeReference(Reference $reference): self
    {
        if ($this->reference->removeElement($reference)) {
            // set the owning side to null (unless already changed)
            if ($reference->getArticleLivingThing() === $this) {
                $reference->setArticleLivingThing(null);
            }
        }

        return $this;
    }
}
