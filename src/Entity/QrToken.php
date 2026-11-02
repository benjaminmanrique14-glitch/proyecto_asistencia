<?php

namespace App\Entity;

use App\Repository\QrTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QrTokenRepository::class)
 * @ORM\Table(name="qr_tokens", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="token", columns={"token"})
 * }, indexes={
 *     @ORM\Index(name="profesor_id", columns={"profesor_id"})
 * })
 */
class QrToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="qrTokens")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesor;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\Column(name="is_used", type="boolean", nullable=true, options={"default": false})
     */
    private $isUsed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfesor(): ?Profesor
    {
        return $this->profesor;
    }

    public function setProfesor(?Profesor $profesor): self
    {
        $this->profesor = $profesor;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): self
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    public function isValid(): bool
    {
        return !$this->isUsed && $this->expiresAt > new \DateTime();
    }
}
