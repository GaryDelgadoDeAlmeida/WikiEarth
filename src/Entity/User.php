<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      maxMessage = "Your firstname cannot be longer than {{ limit }} character",
     *      minMessage = "Your firstname must be at least {{ limit }} characters long"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      maxMessage = "Your lastname cannot be longer than {{ limit }} character",
     *      minMessage = "Your lastname must be at least {{ limit }} characters long"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Your email must be at least {{ limit }} characters long",
     *      maxMessage = "Your email cannot be higher than {{ limit }} characters"
     * )
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 8,
     *      max = 255,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be higher than {{ limit }} characters"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgPath;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="user", cascade={"persist"})
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender")
     */
    private $messages;

    /**
     * @ORM\ManyToMany(targetEntity=ChatRoom::class, mappedBy="user")
     */
    private $chatRooms;

    /**
     * @ORM\OneToMany(targetEntity=ChatRoom::class, mappedBy="participant")
     */
    private $participantChatRoom;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->chatRooms = new ArrayCollection();
        $this->chat = new ArrayCollection();
        $this->participantChatRoom = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUserId($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUserId() === $this) {
                $notification->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChatRoom[]
     */
    public function getChatRooms(): Collection
    {
        return $this->chatRooms;
    }

    public function addChatRoom(ChatRoom $chatRoom): self
    {
        if (!$this->chatRooms->contains($chatRoom)) {
            $this->chatRooms[] = $chatRoom;
            $chatRoom->addUser($this);
        }

        return $this;
    }

    public function removeChatRoom(ChatRoom $chatRoom): self
    {
        if ($this->chatRoom->removeElement($chatRoom)) {
            // set the owning side to null (unless already changed)
            if ($chatRoom->getUserId() === $this) {
                $chatRoom->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChatRoom[]
     */
    public function getParticipantChatRoom(): Collection
    {
        return $this->participantChatRoom;
    }

    public function addParticipantChatRoom(ChatRoom $participantChatRoom): self
    {
        if (!$this->participantChatRoom->contains($participantChatRoom)) {
            $this->participantChatRoom[] = $participantChatRoom;
            $participantChatRoom->setParticipantId($this);
        }

        return $this;
    }

    public function removeParticipantChatRoom(ChatRoom $participantChatRoom): self
    {
        if ($this->participantChatRoom->removeElement($participantChatRoom)) {
            // set the owning side to null (unless already changed)
            if ($participantChatRoom->getParticipantId() === $this) {
                $participantChatRoom->setParticipantId(null);
            }
        }

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

    public function getSalt()
    {
        # code...
    }

    public function getUsername()
    {
        # code...
    }

    public function eraseCredentials()
    {
        # code...
    }

    /**
     * @param string user role
     * @return bool
     */
    public function hasRole(string $role)
    {
        $hasRole = false;

        if(in_array($role, $this->getRoles(), true)) {
            $hasRole = true;
        }

        return $hasRole;
    }
}
