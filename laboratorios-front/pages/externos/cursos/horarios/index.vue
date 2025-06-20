<template>
	<div>
		<Breadcrumbs id="externosCursosHorarios" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="horariosLoading" :campos="horariosCampos" :listado="horariosListado">
			<template v-slot:top-title>
				<div v-if="horariosSelectedCurso" class="mr-sm-3 h6 mb-0 text-center">
					<span class="text-primary">Curso</span>
					<span class="ml-1 ml-sm-4">{{ horariosSelectedCurso.cuexNombre }}</span>
					<span class="ml-2 ml-sm-4 text-primary">Alumnos</span>
					<span class="ml-1 ml-sm-4">{{ horariosSelectedCurso.cuexAlumnosInscritos }}</span>
					<template v-if="horariosSelectedCurso.instructorExternoDTO">
						<span class="ml-2 ml-sm-4 text-primary">Instructor</span>
						<span
							class="ml-1 ml-sm-4"
							:id="`popover-ins-${horariosSelectedCurso.cuexIdCursoExterno}-${horariosSelectedCurso.instructorExternoDTO.inexIdInstructorExterno}`"
							>{{ horariosSelectedCurso.instructorExternoDTO.inexNombre }}
							{{ horariosSelectedCurso.instructorExternoDTO.inexApaterno }}
							{{ horariosSelectedCurso.instructorExternoDTO.inexAmaterno }}</span
						>
						<b-popover
							:target="`popover-ins-${horariosSelectedCurso.cuexIdCursoExterno}-${horariosSelectedCurso.instructorExternoDTO.inexIdInstructorExterno}`"
							triggers="hover"
							placement="top"
						>
							<div>
								<span class="text-smaller text-muted mr-1">Instructor</span>
								{{ horariosSelectedCurso.instructorExternoDTO.inexNombre }}
								{{ horariosSelectedCurso.instructorExternoDTO.inexApaterno }}
								{{ horariosSelectedCurso.instructorExternoDTO.inexAmaterno }}
							</div>
							<div>
								<span class="text-smaller text-muted mr-1">Teléfono</span>
								{{ horariosSelectedCurso.instructorExternoDTO.inexTelefono }}
							</div>
							<div>
								<span class="text-smaller text-muted mr-1">Correo</span>
								{{ horariosSelectedCurso.instructorExternoDTO.inexCorreo }}
							</div>
						</b-popover>
					</template>
					<template v-else>
						<span class="ml-2 ml-sm-4 text-secondary text-small">Sin instructor asignado</span>
					</template>
				</div>
			</template>

			<template v-slot:top-buttons>
				<b-button
					variant="outline-primary"
					size="sm"
					class="ml-3"
					@click="$router.push('/externos/cursos/horarios/semana')"
				>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarSemana(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="eliminarSemana(item)"
						v-b-tooltip.hover.top.ds300
						title="Eliminar"
						><b-icon icon="trash"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>
	</div>
</template>

<script src="./index.js"></script>