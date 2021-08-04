<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(min=10, max=10)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=180)
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=180)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180)
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=180)
     */
    private $firstName;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sendEmail = false;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=Client::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity=Performer::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="performer_id", referencedColumnName="id", nullable=true)
     */
    private $performer;

    /**
     * @ORM\OneToOne(targetEntity=Partner::class, mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="partner_id", referencedColumnName="id", nullable=true)
     */
    private $partner;

    /**
     * @ORM\OneToMany(targetEntity=OrderComment::class, mappedBy="user", orphanRemoval=true)
     */
    private $orderComments;

    public function __construct()
    {
        $this->orderComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function setFacebookId($facebookId): self
    {
        $this->facebookId = $facebookId;

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

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        // set the owning side of the relation if necessary
        if ($client->getUser() !== $this) {
            $client->setUser($this);
        }

        return $this;
    }

    public function getPerformer(): ?Performer
    {
        return $this->performer;
    }

    public function setPerformer(Performer $user): self
    {
        $this->performer = $user;

        // set the owning side of the relation if necessary
        if ($user->getUser() !== $this) {
            $user->setUser($this);
        }

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $user): self
    {
        $this->partner = $user;

        // set the owning side of the relation if necessary
        if ($user->getUser() !== $this) {
            $user->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|OrderComment[]
     */
    public function getOrderComments(): Collection
    {
        return $this->orderComments;
    }

    public function addOrderComment(OrderComment $orderComment): self
    {
        if (!$this->orderComments->contains($orderComment)) {
            $this->orderComments[] = $orderComment;
            $orderComment->setUser($this);
        }

        return $this;
    }

    public function removeOrderComment(OrderComment $orderComment): self
    {
        if ($this->orderComments->removeElement($orderComment)) {
            // set the owning side to null (unless already changed)
            if ($orderComment->getUser() === $this) {
                $orderComment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @Assert\IsTrue(message="The password cannot match your first name")
     */
    public function isPasswordSafe()
    {
        return $this->firstName !== $this->password;
    }
}
