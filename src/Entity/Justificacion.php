<?php

namespace App\Entity;

use App\Repository\JustificacionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JustificacionRepository::class)
 * @ORM\Table(name="justificaciones", indexes={
 *     @ORM\Index(name="profesor_id", columns={"profesor_id"})
 * })
 */
class Justificacion
{
    public const TIPO_FALTA_DIA = 'falta_dia';
    public const TIPO_RETIRO_TEMPRANO = 'retiro_temprano';
    public const TIPO_FALTA_FUTURA = 'falta_futura';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="justificaciones")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesor;

    /**
     * @ORM\Column(type="string", length=20, columnDefinition="ENUM('falta_dia','retiro_temprano','falta_futura') NOT NULL")
     */
    private $tipo;

    /**
     * @ORM\Column(name="fecha_afectada", type="date")
     */
    private $fechaAfectada;

    /**
     * @ORM\Column(type="text", columnDefinition="TEXT NOT NULL")
     */
    private $motivo;

    /**
     * @ORM\Column(name="archivo_url", type="string", length=255)
     */
    private $archivoUrl;

    /**
     * @ORM\Column(name="created_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public static function getTipos(): array
    {
        return [
            self::TIPO_FALTA_DIA => 'Falta del día',
            self::TIPO_RETIRO_TEMPRANO => 'Retiro temprano',
            self::TIPO_FALTA_FUTURA => 'Falta futura',
        ];
    }

    public function getTipoLabel(): string
    {
        $tipos = self::getTipos();

        return $tipos[$this->tipo] ?? $this->tipo;
    }

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getFechaAfectada(): ?\DateTimeInterface
    {
        return $this->fechaAfectada;
    }

    public function setFechaAfectada(\DateTimeInterface $fechaAfectada): self
    {
        $this->fechaAfectada = $fechaAfectada;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getArchivoUrl(): ?string
    {
        return $this->archivoUrl;
    }

    public function setArchivoUrl(string $archivoUrl): self
    {
        $this->archivoUrl = $archivoUrl;

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
}
