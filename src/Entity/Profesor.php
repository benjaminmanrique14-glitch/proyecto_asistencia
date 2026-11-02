<?php

namespace App\Entity;

use App\Repository\ProfesorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ProfesorRepository::class)
 * @ORM\Table(name="profesores", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="dni", columns={"dni"})
 * })
 */
class Profesor implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $apellido;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=true, options={"default": true})
     */
    private $isActive = true;

    /**
     * @ORM\Column(name="created_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=ProfesorCurso::class, mappedBy="profesor", cascade={"persist", "remove"})
     */
    private $asignaciones;

    /**
     * @ORM\OneToMany(targetEntity=HorarioProfesor::class, mappedBy="profesor", cascade={"persist", "remove"})
     */
    private $horarios;

    /**
     * @ORM\OneToMany(targetEntity=Asistencia::class, mappedBy="profesor")
     */
    private $asistencias;

    /**
     * @ORM\OneToMany(targetEntity=Justificacion::class, mappedBy="profesor")
     */
    private $justificaciones;

    /**
     * @ORM\OneToMany(targetEntity=QrToken::class, mappedBy="profesor")
     */
    private $qrTokens;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->asignaciones = new ArrayCollection();
        $this->horarios = new ArrayCollection();
        $this->asistencias = new ArrayCollection();
        $this->justificaciones = new ArrayCollection();
        $this->qrTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getNombreCompleto(): string
    {
        return trim($this->apellido.' '.$this->nombre);
    }

    public function getRoles(): array
    {
        return ['ROLE_PROFESOR'];
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->dni;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
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

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getIniciales(): string
    {
        $nombre = mb_substr(trim((string) $this->nombre), 0, 1);
        $apellido = mb_substr(trim((string) $this->apellido), 0, 1);

        return mb_strtoupper($apellido.$nombre);
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    /**
     * @return Collection<int, ProfesorCurso>
     */
    public function getAsignaciones(): Collection
    {
        return $this->asignaciones;
    }

    public function addAsignacion(ProfesorCurso $asignacion): self
    {
        if (!$this->asignaciones->contains($asignacion)) {
            $this->asignaciones[] = $asignacion;
            $asignacion->setProfesor($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, HorarioProfesor>
     */
    public function getHorarios(): Collection
    {
        return $this->horarios;
    }

    public function addHorario(HorarioProfesor $horario): self
    {
        if (!$this->horarios->contains($horario)) {
            $this->horarios[] = $horario;
            $horario->setProfesor($this);
        }

        return $this;
    }

    public function removeHorario(HorarioProfesor $horario): self
    {
        if ($this->horarios->removeElement($horario) && $horario->getProfesor() === $this) {
            $horario->setProfesor(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Asistencia>
     */
    public function getAsistencias(): Collection
    {
        return $this->asistencias;
    }

    /**
     * @return Collection<int, Justificacion>
     */
    public function getJustificaciones(): Collection
    {
        return $this->justificaciones;
    }

    /**
     * @return Collection<int, QrToken>
     */
    public function getQrTokens(): Collection
    {
        return $this->qrTokens;
    }
}
