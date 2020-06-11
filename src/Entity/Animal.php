<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnimalRepository::class)
 */
class Animal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commonName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kingdom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subKingdom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $branch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subBranch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infraBranch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $division;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $superClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infraClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $superOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $normalOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infraOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $microOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $superFamily;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $family;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subFamily;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subGenus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subSpecies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $animalType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(?string $commonName): self
    {
        $this->commonName = $commonName;

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

    public function getKingdom(): ?string
    {
        return $this->kingdom;
    }

    public function setKingdom(string $kingdom): self
    {
        $this->kingdom = $kingdom;

        return $this;
    }

    public function getSubKingdom(): ?string
    {
        return $this->subKingdom;
    }

    public function setSubKingdom(?string $subKingdom): self
    {
        $this->subKingdom = $subKingdom;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getSubBranch(): ?string
    {
        return $this->subBranch;
    }

    public function setSubBranch(?string $subBranch): self
    {
        $this->subBranch = $subBranch;

        return $this;
    }

    public function getInfraBranch(): ?string
    {
        return $this->infraBranch;
    }

    public function setInfraBranch(?string $infraBranch): self
    {
        $this->infraBranch = $infraBranch;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(?string $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function getSuperClass(): ?string
    {
        return $this->superClass;
    }

    public function setSuperClass(?string $superClass): self
    {
        $this->superClass = $superClass;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getSubClass(): ?string
    {
        return $this->subClass;
    }

    public function setSubClass(?string $subClass): self
    {
        $this->subClass = $subClass;

        return $this;
    }

    public function getInfraClass(): ?string
    {
        return $this->infraClass;
    }

    public function setInfraClass(?string $infraClass): self
    {
        $this->infraClass = $infraClass;

        return $this;
    }

    public function getSuperOrder(): ?string
    {
        return $this->superOrder;
    }

    public function setSuperOrder(?string $superOrder): self
    {
        $this->superOrder = $superOrder;

        return $this;
    }

    public function getNormalOrder(): ?string
    {
        return $this->normalOrder;
    }

    public function setNormalOrder(?string $normalOrder): self
    {
        $this->normalOrder = $normalOrder;

        return $this;
    }

    public function getSubOrder(): ?string
    {
        return $this->subOrder;
    }

    public function setSubOrder(?string $subOrder): self
    {
        $this->subOrder = $subOrder;

        return $this;
    }

    public function getInfraOrder(): ?string
    {
        return $this->infraOrder;
    }

    public function setInfraOrder(?string $infraOrder): self
    {
        $this->infraOrder = $infraOrder;

        return $this;
    }

    public function getMicroOrder(): ?string
    {
        return $this->microOrder;
    }

    public function setMicroOrder(?string $microOrder): self
    {
        $this->microOrder = $microOrder;

        return $this;
    }

    public function getSuperFamily(): ?string
    {
        return $this->superFamily;
    }

    public function setSuperFamily(?string $superFamily): self
    {
        $this->superFamily = $superFamily;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getSubFamily(): ?string
    {
        return $this->subFamily;
    }

    public function setSubFamily(?string $subFamily): self
    {
        $this->subFamily = $subFamily;

        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(?string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getSubGenus(): ?string
    {
        return $this->subGenus;
    }

    public function setSubGenus(?string $subGenus): self
    {
        $this->subGenus = $subGenus;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(?string $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getSubSpecies(): ?string
    {
        return $this->subSpecies;
    }

    public function setSubSpecies(?string $subSpecies): self
    {
        $this->subSpecies = $subSpecies;

        return $this;
    }

    public function getAnimalType(): ?string
    {
        return $this->animalType;
    }

    public function setAnimalType(string $animalType): self
    {
        $this->animalType = $animalType;

        return $this;
    }
}
