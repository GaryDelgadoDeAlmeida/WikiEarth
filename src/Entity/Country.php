<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $treatedCountryName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $alpha2Code;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $alpha3Code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subRegion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nativeName;

    /**
     * @ORM\ManyToMany(targetEntity=ArticleLivingThing::class, inversedBy="countries")
     */
    private $articleLivingThing;

    /**
     * @ORM\ManyToMany(targetEntity=Mineral::class, mappedBy="country")
     */
    private $minerals;

    public function __construct()
    {
        $this->articleLivingThing = new ArrayCollection();
        $this->minerals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTreatedCountryName(): ?string
    {
        return $this->treatedCountryName;
    }

    public function setTreatedCountryName(string $treatedCountryName): self
    {
        $this->treatedCountryName = $treatedCountryName;

        return $this;
    }

    public function getAlpha2Code(): ?string
    {
        return $this->alpha2Code;
    }

    public function setAlpha2Code(?string $alpha2Code): self
    {
        $this->alpha2Code = $alpha2Code;

        return $this;
    }

    public function getAlpha3Code(): ?string
    {
        return $this->alpha3Code;
    }

    public function setAlpha3Code(?string $alpha3Code): self
    {
        $this->alpha3Code = $alpha3Code;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getSubRegion(): ?string
    {
        return $this->subRegion;
    }

    public function setSubRegion(?string $subRegion): self
    {
        $this->subRegion = $subRegion;

        return $this;
    }

    public function getNativeName(): ?string
    {
        return $this->nativeName;
    }

    public function setNativeName(?string $nativeName): self
    {
        $this->nativeName = $nativeName;

        return $this;
    }

    /**
     * @return Collection|ArticleLivingThing[]
     */
    public function getArticleLivingThing(): Collection
    {
        return $this->articleLivingThing;
    }

    public function addArticleLivingThing(ArticleLivingThing $articleLivingThing): self
    {
        if (!$this->articleLivingThing->contains($articleLivingThing)) {
            $this->articleLivingThing[] = $articleLivingThing;
        }

        return $this;
    }

    public function removeArticleLivingThing(ArticleLivingThing $articleLivingThing): self
    {
        if ($this->articleLivingThing->contains($articleLivingThing)) {
            $this->articleLivingThing->removeElement($articleLivingThing);
        }

        return $this;
    }

    /**
     * @return Collection|Mineral[]
     */
    public function getMinerals(): Collection
    {
        return $this->minerals;
    }

    public function addMineral(Mineral $mineral): self
    {
        if (!$this->minerals->contains($mineral)) {
            $this->minerals[] = $mineral;
            $mineral->addCountry($this);
        }

        return $this;
    }

    public function removeMineral(Mineral $mineral): self
    {
        if ($this->minerals->contains($mineral)) {
            $this->minerals->removeElement($mineral);
            $mineral->removeCountry($this);
        }

        return $this;
    }
}
