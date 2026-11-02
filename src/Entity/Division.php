<?php

namespace App\Entity;

use App\Repository\DivisionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DivisionRepository::class)
 * @ORM\Table(name="divisiones", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="numero", columns={"numero"})
 * })
 */
class Division
{
    public const TURNO_MANANA = 'manana';
    public const TURNO_TARDE = 'tarde';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", columnDefinition="TINYINT(4) NOT NULL")
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=10, columnDefinition="ENUM('manana','tarde') NOT NULL")
     */
    private $turno;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

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

    public function getTurno(): ?string
    {
        return $this->turno;
    }

    public function setTurno(string $turno): self
    {
        $this->turno = $turno;

        return $this;
    }

    public function getTurnoLabel(): string
    {
        return self::TURNO_TARDE === $this->turno ? 'Tarde' : 'Mañana';
    }

    public function getHoraEntradaSugerida(): string
    {
        return self::TURNO_TARDE === $this->turno ? '13:10' : '08:00';
    }

    public function getHoraSalidaSugerida(): string
    {
        return self::TURNO_TARDE === $this->turno ? '17:30' : '12:30';
    }
}
