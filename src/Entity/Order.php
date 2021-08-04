<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Performer::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $performer;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $summeryType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $theme;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $requirements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $doc;

    /**
     * @ORM\Column(type="date")
     */
    private $deadline;

    /**
     * @ORM\OneToMany(targetEntity=OrderComment::class, mappedBy="order", orphanRemoval=true)
     */
    private $orderComments;

    /**
     * @ORM\OneToOne(targetEntity=Review::class, mappedBy="reviewedOrder", cascade={"persist", "remove"})
     */
    private $reviews;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="oder")
     */
    private $transactions;

    public function __construct()
    {
        $this->orderComments = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerformer(): ?Performer
    {
        return $this->performer;
    }

    public function setPerformer(?Performer $performer): self
    {
        $this->performer = $performer;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getSummeryType(): ?string
    {
        return $this->summeryType;
    }

    public function setSummeryType(string $summeryType): self
    {
        $this->summeryType = $summeryType;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequirements(): ?string
    {
        return $this->requirements;
    }

    public function setRequirements(?string $requirements): self
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDoc(): ?string
    {
        return $this->doc;
    }

    public function setDoc($doc): self
    {
        $this->doc = $doc;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

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
            $orderComment->setOrder($this);
        }

        return $this;
    }

    public function removeOrderComment(OrderComment $orderComment): self
    {
        if ($this->orderComments->contains($orderComment)) {
            $this->orderComments->removeElement($orderComment);
            // set the owning side to null (unless already changed)
            if ($orderComment->getOrder() === $this) {
                $orderComment->setOrder(null);
            }
        }

        return $this;
    }

    public function getReviews(): ?Review
    {
        return $this->reviews;
    }

    public function setReviews(Review $reviews): self
    {
        $this->reviews = $reviews;

        // set the owning side of the relation if necessary
        if ($reviews->getReviewedOrder() !== $this) {
            $reviews->setReviewedOrder($this);
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setOderr($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getOderr() === $this) {
                $transaction->setOderr(null);
            }
        }

        return $this;
    }
}
