<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CouponRepository::class)
 * @UniqueEntity("code")
 */
class Coupon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $percent_off;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $validateDays;

    /**
     * @ORM\Column(type="smallint")
     */
    private $usersNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPercentOff(): ?float
    {
        return $this->percent_off;
    }

    public function setPercentOff(?float $percent_off): self
    {
        $this->percent_off = $percent_off;

        return $this;
    }

    public function discount($total)
    {
        if($this->type == 'fixed'){
            return $this->value / 100;
        }elseif($this->type == 'percent'){
            return ($this->percent_off * $total);
        }else{
            return 0;
        }
    }

    public function discountValue()
    {
        if($this->type == 'fixed'){
            return ($this->value / 100);
        }else{
            return ($this->percent_off * 100);
        }
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getValidateDays(): ?int
    {
        return $this->validateDays;
    }

    public function setValidateDays(int $validateDays): self
    {
        $this->validateDays = $validateDays;

        return $this;
    }

    public function getUsersNumber(): ?int
    {
        return $this->usersNumber;
    }

    public function setUsersNumber(int $usersNumber): self
    {
        $this->usersNumber = $usersNumber;

        return $this;
    }
}
