<?php

namespace App\Entity;

use App\Repository\AtomeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AtomeRepository::class)
 */
class Atome
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
    private $scientificName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $radioisotope;

    /**
     * @ORM\Column(type="integer")
     */
    private $atomicNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $symbole;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $atomeGroup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $atomePeriod;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $atomeBlock;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $volumicMass;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numCAS;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numCE;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=10)
     */
    private $atomicMass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $atomicRadius;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $covalentRadius;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vanDerWaalsRadius;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $electroniqueConfiguration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $oxidationState;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $electronegativity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fusionPoint;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $boilingPoint;

    /**
     * @ORM\Column(type="boolean")
     */
    private $radioactivity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgPath;

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

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): self
    {
        $this->scientificName = $scientificName;

        return $this;
    }

    public function getRadioisotope(): ?string
    {
        return $this->radioisotope;
    }

    public function setRadioisotope(string $radioisotope): self
    {
        $this->radioisotope = $radioisotope;

        return $this;
    }

    public function getAtomicNumber(): ?int
    {
        return $this->atomicNumber;
    }

    public function setAtomicNumber(int $atomicNumber): self
    {
        $this->atomicNumber = $atomicNumber;

        return $this;
    }

    public function getSymbole(): ?string
    {
        return $this->symbole;
    }

    public function setSymbole(string $symbole): self
    {
        $this->symbole = $symbole;

        return $this;
    }

    public function getAtomeGroup(): ?string
    {
        return $this->atomeGroup;
    }

    public function setAtomeGroup(?string $atomeGroup): self
    {
        $this->atomeGroup = $atomeGroup;

        return $this;
    }

    public function getAtomePeriod(): ?string
    {
        return $this->atomePeriod;
    }

    public function setAtomePeriod(string $atomePeriod): self
    {
        $this->atomePeriod = $atomePeriod;

        return $this;
    }

    public function getAtomeBlock(): ?string
    {
        return $this->atomeBlock;
    }

    public function setAtomeBlock(string $atomeBlock): self
    {
        $this->atomeBlock = $atomeBlock;

        return $this;
    }

    public function getVolumicMass(): ?string
    {
        return $this->volumicMass;
    }

    public function setVolumicMass(?string $volumicMass): self
    {
        $this->volumicMass = $volumicMass;

        return $this;
    }

    public function getNumCAS(): ?string
    {
        return $this->numCAS;
    }

    public function setNumCAS(string $numCAS): self
    {
        $this->numCAS = $numCAS;

        return $this;
    }

    public function getNumCE(): ?string
    {
        return $this->numCE;
    }

    public function setNumCE(?string $numCE): self
    {
        $this->numCE = $numCE;

        return $this;
    }

    public function getAtomicMass(): ?string
    {
        return $this->atomicMass;
    }

    public function setAtomicMass(string $atomicMass): self
    {
        $this->atomicMass = $atomicMass;

        return $this;
    }

    public function getAtomicRadius(): ?string
    {
        return $this->atomicRadius;
    }

    public function setAtomicRadius(?string $atomicRadius): self
    {
        $this->atomicRadius = $atomicRadius;

        return $this;
    }

    public function getCovalentRadius(): ?string
    {
        return $this->covalentRadius;
    }

    public function setCovalentRadius(?string $covalentRadius): self
    {
        $this->covalentRadius = $covalentRadius;

        return $this;
    }

    public function getVanDerWaalsRadius(): ?string
    {
        return $this->vanDerWaalsRadius;
    }

    public function setVanDerWaalsRadius(?string $vanDerWaalsRadius): self
    {
        $this->vanDerWaalsRadius = $vanDerWaalsRadius;

        return $this;
    }

    public function getElectroniqueConfiguration(): ?string
    {
        return $this->electroniqueConfiguration;
    }

    public function setElectroniqueConfiguration(?string $electroniqueConfiguration): self
    {
        $this->electroniqueConfiguration = $electroniqueConfiguration;

        return $this;
    }

    public function getOxidationState(): ?string
    {
        return $this->oxidationState;
    }

    public function setOxidationState(?string $oxidationState): self
    {
        $this->oxidationState = $oxidationState;

        return $this;
    }

    public function getElectronegativity(): ?string
    {
        return $this->electronegativity;
    }

    public function setElectronegativity(?string $electronegativity): self
    {
        $this->electronegativity = $electronegativity;

        return $this;
    }

    public function getFusionPoint(): ?string
    {
        return $this->fusionPoint;
    }

    public function setFusionPoint(?string $fusionPoint): self
    {
        $this->fusionPoint = $fusionPoint;

        return $this;
    }

    public function getBoilingPoint(): ?string
    {
        return $this->boilingPoint;
    }

    public function setBoilingPoint(?string $boilingPoint): self
    {
        $this->boilingPoint = $boilingPoint;

        return $this;
    }

    public function getRadioactivity(): ?bool
    {
        return $this->radioactivity;
    }

    public function setRadioactivity(bool $radioactivity): self
    {
        $this->radioactivity = $radioactivity;

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
}
