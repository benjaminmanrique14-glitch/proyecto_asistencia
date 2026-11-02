<?php

namespace App\Entity;

use App\Repository\HorarioProfesorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HorarioProfesorRepository::class)
 * @ORM\Table(name="horarios_profesores", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unico_horario_curso_dia", columns={"profesor_curso_id", "dia_semana"})
 * }, indexes={
 *     @ORM\Index(name="profesor_id", columns={"profesor_id"})
 * })
 */
class HorarioProfesor
{
    public const DIA_LUNES = 1;
    public const DIA_MARTES = 2;
    public const DIA_MIERCOLES = 3;
    public const DIA_JUEVES = 4;
    public const DIA_VIERNES = 5;

    public static function getDias(): array
    {
        return [
            self::DIA_LUNES => 'Lunes',
            self::DIA_MARTES => 'Martes',
            self::DIA_MIERCOLES => 'Miércoles',
            self::DIA_JUEVES => 'Jueves',
            self::DIA_VIERNES => 'Viernes',
        ];
    }

    public function getNombreDia(): string
    {
        $dias = self::getDias();

        return $dias[$this->diaSemana] ?? (string) $this->diaSemana;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="horarios")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesor;

    /**
     * @ORM\ManyToOne(targetEntity=ProfesorCurso::class, inversedBy="horarios")
     * @ORM\JoinColumn(name="profesor_curso_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesorCurso;

    /**
     * @ORM\Column(name="dia_semana", type="smallint", columnDefinition="TINYINT(4) NOT NULL")
     */
    private $diaSemana;

    /**
     * @ORM\Column(name="hora_entrada", type="time")
     */
    private $horaEntrada;

    /**
     * @ORM\Column(name="hora_salida", type="time")
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

    public function getProfesorCurso(): ?ProfesorCurso
    {
        return $this->profesorCurso;
    }

    public function setProfesorCurso(?ProfesorCurso $profesorCurso): self
    {
        $this->profesorCurso = $profesorCurso;
        if ($profesorCurso && $profesorCurso->getProfesor()) {
            $this->setProfesor($profesorCurso->getProfesor());
        }

        return $this;
    }

    public function getDiaSemana(): ?int
    {
        return $this->diaSemana;
    }

    public function setDiaSemana(int $diaSemana): self
    {
        $this->diaSemana = $diaSemana;

        return $this;
    }

    public function getHoraEntrada(): ?\DateTimeInterface
    {
        return $this->horaEntrada;
    }

    public function setHoraEntrada(\DateTimeInterface $horaEntrada): self
    {
        $this->horaEntrada = $horaEntrada;

        return $this;
    }

    public function getHoraSalida(): ?\DateTimeInterface
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(\DateTimeInterface $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }
}
