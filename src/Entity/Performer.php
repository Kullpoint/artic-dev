<?php

namespace App\Entity;

use App\Repository\PerformerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PerformerRepository::class)
 */
class Performer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $balance;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $pendingBalance;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $experience;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $summeryTypes;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $rating;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="performer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="performer", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="performer")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity=Complaint::class, mappedBy="performer")
     */
    private $complaints;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="receiver")
     */
    private $transactions;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="performers")
     */
    private $categories;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getPendingBalance()
    {
        return $this->pendingBalance;
    }

    public function setPendingBalance($pendingBalance): self
    {
        $this->pendingBalance = $pendingBalance;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAge()
    {
        $now = new \DateTime('now');
        $age = $this->getBirthday();
        $difference = $now->diff($age);

        return $difference->format('%y');
    }

    public function getAbout()
    {
        return $this->about;
    }

    public function setAbout($about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setExperience($experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getSummeryTypes()
    {
        return $this->summeryTypes;
    }

    public function setSummeryTypes($summeryTypes): self
    {
        $this->summeryTypes = $summeryTypes;

        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setPerformer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getPerformer() === $this) {
                $order->setPerformer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setPerformer($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getPerformer() === $this) {
                $review->setPerformer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Complaint[]
     */
    public function getComplaints(): Collection
    {
        return $this->complaints;
    }

    public function addComplaint(Complaint $complaint): self
    {
        if (!$this->complaints->contains($complaint)) {
            $this->complaints[] = $complaint;
            $complaint->setPerformer($this);
        }

        return $this;
    }

    public function removeComplaint(Complaint $complaint): self
    {
        if ($this->complaints->contains($complaint)) {
            $this->complaints->removeElement($complaint);
            // set the owning side to null (unless already changed)
            if ($complaint->getPerformer() === $this) {
                $complaint->setPerformer(null);
            }
        }

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
            $transaction->setReceiver($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getReceiver() === $this) {
                $transaction->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addPerformer($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removePerformer($this);
        }

        return $this;
    }
}
