<?php

namespace App\Entity;

use App\Repository\AsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AsistenciaRepository::class)
 * @ORM\Table(name="asistencias", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unica_asistencia_dia", columns={"profesor_id", "fecha"})
 * })
 */
class Asistencia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="asistencias")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesor;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(name="hora_ingreso", type="time")
     */
    private $horaIngreso;

    /**
     * @ORM\Column(name="hora_salida", type="time", nullable=true)
     */
    private $horaSalida;

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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHoraIngreso(): ?\DateTimeInterface
    {
        return $this->horaIngreso;
    }

    public function setHoraIngreso(\DateTimeInterface $horaIngreso): self
    {
        $this->horaIngreso = $horaIngreso;

        return $this;
    }

    public function getHoraSalida(): ?\DateTimeInterface
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(?\DateTimeInterface $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }
}
