<?php

namespace App\Entity;

use App\Repository\LivingThingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LivingThingRepository::class)
 */
class LivingThing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The commom name of the animal name cannot be longer than {{ limit }} character"
     * )
     */
    private $commonName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "The name of the animal must be at least {{ limit }} characters long",
     *      maxMessage = "The name of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The domain of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub domain of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subDomain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "The super kingdom of the animal must be at least {{ limit }} characters long",
     *      maxMessage = "The super kingdom of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $superKingdom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "The kingdom of the animal must be at least {{ limit }} characters long",
     *      maxMessage = "The kingdom of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $kingdom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub kingdom of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subKingdom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infraKingdom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $superBranch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The branch of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $branch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub branch of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subBranch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The infra branch of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $infraBranch;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The division of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $division;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The super class of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $superClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The class of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub class of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The infra class of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $infraClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The super order of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $superOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The order of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $normalOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub order of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The infra order of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $infraOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The micro order of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $microOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The super family of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $superFamily;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The family of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $family;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub family of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subFamily;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tribe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subTribe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The genus of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $genus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub genus of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subGenus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The specie of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The sub specie of the animal cannot be longer than {{ limit }} character"
     * )
     */
    private $subSpecies;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgPath;

    /**
     * @ORM\OneToOne(targetEntity=ArticleLivingThing::class, mappedBy="livingThing", cascade={"persist", "remove"})
     */
    private $articleLivingThing;

    /**
     * @ORM\ManyToMany(targetEntity=Country::class, mappedBy="livingThing")
     */
    private $countries;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }

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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getSubDomain(): ?string
    {
        return $this->subDomain;
    }

    public function setSubDomain(?string $subDomain): self
    {
        $this->subDomain = $subDomain;

        return $this;
    }

    public function getSuperKingdom(): ?string
    {
        return $this->superKingdom;
    }

    public function setSuperKingdom(?string $superKingdom): self
    {
        $this->superKingdom = $superKingdom;

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

    public function getInfraKingdom(): ?string
    {
        return $this->infraKingdom;
    }

    public function setInfraKingdom(?string $infraKingdom): self
    {
        $this->infraKingdom = $infraKingdom;

        return $this;
    }

    public function getSuperBranch(): ?string
    {
        return $this->superBranch;
    }

    public function setSuperBranch(?string $superBranch): self
    {
        $this->superBranch = $superBranch;

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

    public function getTribe(): ?string
    {
        return $this->tribe;
    }

    public function setTribe(?string $tribe): self
    {
        $this->tribe = $tribe;

        return $this;
    }

    public function getSubTribe(): ?string
    {
        return $this->subTribe;
    }

    public function setSubTribe(?string $subTribe): self
    {
        $this->subTribe = $subTribe;

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

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function setImgPath(string $imgPath): self
    {
        $this->imgPath = $imgPath;

        return $this;
    }

    public function getArticleLivingThing(): ?ArticleLivingThing
    {
        return $this->articleLivingThing;
    }

    public function setArticleLivingThing(?ArticleLivingThing $articleLivingThing): self
    {
        $this->articleLivingThing = $articleLivingThing;

        // set (or unset) the owning side of the relation if necessary
        $newIdLivingThing = null === $articleLivingThing ? null : $this;
        if ($articleLivingThing->getLivingThing() !== $newIdLivingThing) {
            $articleLivingThing->setLivingThing($newIdLivingThing);
        }

        return $this;
    }

    /**
     * @return Collection|Country[]
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
            $country->addLivingThing($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->contains($country)) {
            $this->countries->removeElement($country);
            $country->removeLivingThing($this);
        }

        return $this;
    }
}
