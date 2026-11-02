<?php

namespace App\Entity;

use App\Repository\ProfesorCursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfesorCursoRepository::class)
 * @ORM\Table(name="profesor_cursos", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unica_asignacion", columns={"profesor_id", "curso_id", "division_id"})
 * }, indexes={
 *     @ORM\Index(name="curso_id", columns={"curso_id"}),
 *     @ORM\Index(name="division_id", columns={"division_id"})
 * })
 */
class ProfesorCurso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Profesor::class, inversedBy="asignaciones")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $profesor;

    /**
     * @ORM\ManyToOne(targetEntity=Curso::class)
     * @ORM\JoinColumn(name="curso_id", referencedColumnName="id", nullable=false)
     */
    private $curso;

    /**
     * @ORM\ManyToOne(targetEntity=Division::class)
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id", nullable=false)
     */
    private $division;

    /**
     * @ORM\OneToMany(targetEntity=HorarioProfesor::class, mappedBy="profesorCurso", cascade={"persist", "remove"})
     */
    private $horarios;

    public function __construct()
    {
        $this->horarios = new ArrayCollection();
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

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function getEtiqueta(): string
    {
        return trim(($this->curso ? $this->curso->getNombre() : '').' '.($this->division ? $this->division->getNombre() : ''));
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
            $horario->setProfesorCurso($this);
        }

        return $this;
    }
}
