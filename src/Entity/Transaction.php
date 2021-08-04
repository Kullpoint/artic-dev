<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Performer::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity=Performer::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $partner;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chargeId;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReceiver(): ?Performer
    {
        return $this->receiver;
    }

    public function setReceiver(?Performer $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getSender(): ?Client
    {
        return $this->sender;
    }

    public function setSender(?Client $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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

    public function getPaid()
    {
        return $this->paid;
    }

    public function setPaid($paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getChargeId()
    {
        return $this->chargeId;
    }

    public function setChargeId($chargeId): self
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getCommission(): ?int
    {
        return $this->commission;
    }

    public function setCommission(int $commission): self
    {
        $this->commission = $commission;

        return $this;
    }
}
