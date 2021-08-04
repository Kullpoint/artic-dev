<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Performer::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $performer;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="reviews", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $reviewedOrder;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mark;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=Complaint::class, cascade={"persist", "remove"})
     */
    private $complaint;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPerformer(): ?Performer
    {
        return $this->performer;
    }

    public function setPerformer(?Performer $performer): self
    {
        $this->performer = $performer;

        return $this;
    }

    public function getReviewedOrder(): ?Order
    {
        return $this->reviewedOrder;
    }

    public function setReviewedOrder(Order $reviewedOrder): self
    {
        $this->reviewedOrder = $reviewedOrder;

        return $this;
    }

    public function getMark(): ?string
    {
        return $this->mark;
    }

    public function setMark(string $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getComplaint(): ?Complaint
    {
        return $this->complaint;
    }

    public function setComplaint(?Complaint $complaint): self
    {
        $this->complaint = $complaint;

        return $this;
    }


}
