<?php

namespace App\Entity;

use App\Repository\StatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatisticsRepository::class)
 */
class Statistics
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrAnonymousConnection;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrUsersConnection;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrPageConsultations;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrArticleCreations;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrAnonymousConnection(): ?int
    {
        return $this->nbrAnonymousConnection;
    }

    public function setNbrAnonymousConnection(int $nbrAnonymousConnection): self
    {
        $this->nbrAnonymousConnection = $nbrAnonymousConnection;

        return $this;
    }

    public function getNbrUsersConnection(): ?int
    {
        return $this->nbrUsersConnection;
    }

    public function setNbrUsersConnection(int $nbrUsersConnection): self
    {
        $this->nbrUsersConnection = $nbrUsersConnection;

        return $this;
    }

    public function getNbrPageConsultations(): ?int
    {
        return $this->nbrPageConsultations;
    }

    public function setNbrPageConsultations(int $nbrPageConsultations): self
    {
        $this->nbrPageConsultations = $nbrPageConsultations;

        return $this;
    }

    public function getNbrArticleCreations(): ?int
    {
        return $this->nbrArticleCreations;
    }

    public function setNbrArticleCreations(int $nbrArticleCreations): self
    {
        $this->nbrArticleCreations = $nbrArticleCreations;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
