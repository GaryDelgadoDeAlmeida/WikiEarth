<?php

namespace App\Entity;

use App\Repository\MineralRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MineralRepository::class)
 */
class Mineral
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=ArticleMineral::class, mappedBy="mineral", cascade={"persist", "remove"})
     */
    private $articleMineral;

    /**
     * @ORM\ManyToMany(targetEntity=Country::class, inversedBy="minerals")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rruffChemistry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imaChemistry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chemistryElements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imaNumber;

    /**
     * @ORM\Column(type="json")
     */
    private $imaStatus = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $structuralGroupname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $crystalSystem;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valenceElements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgPath;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->country = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleMineral(): ?ArticleMineral
    {
        return $this->articleMineral;
    }

    public function setArticleMineral(?ArticleMineral $articleMineral): self
    {
        // unset the owning side of the relation if necessary
        if ($articleMineral === null && $this->articleMineral !== null) {
            $this->articleMineral->setMineral(null);
        }

        // set the owning side of the relation if necessary
        if ($articleMineral !== null && $articleMineral->getMineral() !== $this) {
            $articleMineral->setMineral($this);
        }

        $this->articleMineral = $articleMineral;

        return $this;
    }

    /**
     * @return Collection|Country[]
     */
    public function getCountry(): Collection
    {
        return $this->country;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->country->contains($country)) {
            $this->country[] = $country;
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->country->contains($country)) {
            $this->country->removeElement($country);
        }

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

    public function getRruffChemistry(): ?string
    {
        return $this->rruffChemistry;
    }

    public function setRruffChemistry(string $rruffChemistry): self
    {
        $this->rruffChemistry = $rruffChemistry;

        return $this;
    }

    public function getImaChemistry(): ?string
    {
        return $this->imaChemistry;
    }

    public function setImaChemistry(string $imaChemistry): self
    {
        $this->imaChemistry = $imaChemistry;

        return $this;
    }

    public function getChemistryElements(): ?string
    {
        return $this->chemistryElements;
    }

    public function setChemistryElements(string $chemistryElements): self
    {
        $this->chemistryElements = $chemistryElements;

        return $this;
    }

    public function getImaNumber(): ?string
    {
        return $this->imaNumber;
    }

    public function setImaNumber(?string $imaNumber): self
    {
        $this->imaNumber = $imaNumber;

        return $this;
    }

    public function getImaStatus(): ?array
    {
        return $this->imaStatus;
    }

    public function setImaStatus(array $imaStatus): self
    {
        $this->imaStatus = $imaStatus;

        return $this;
    }

    public function getStructuralGroupname(): ?string
    {
        return $this->structuralGroupname;
    }

    public function setStructuralGroupname(?string $structuralGroupname): self
    {
        $this->structuralGroupname = $structuralGroupname;

        return $this;
    }

    public function getCrystalSystem(): ?string
    {
        return $this->crystalSystem;
    }

    public function setCrystalSystem(?string $crystalSystem): self
    {
        $this->crystalSystem = $crystalSystem;

        return $this;
    }

    public function getValenceElements(): ?string
    {
        return $this->valenceElements;
    }

    public function setValenceElements(string $valenceElements): self
    {
        $this->valenceElements = $valenceElements;

        return $this;
    }

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function setImgPath(?string $imgPath): self
    {
        $this->imgPath = $imgPath;

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
