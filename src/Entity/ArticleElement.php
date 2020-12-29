<?php

namespace App\Entity;

use App\Repository\ArticleElementRepository;
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
     * @ORM\OneToOne(targetEntity=Element::class, inversedBy="articleElement", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $element;

    public function getId(): ?int
    {
        return $this->id;
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
}
