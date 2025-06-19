<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

interface IInstructorExternoDAO
{
	public function alta(InstructorExternoDTO $instructorDTO): int;
	public function consultaAll(): array;
	public function modificacion(InstructorExternoDTO $instructorDTO): bool;
}
