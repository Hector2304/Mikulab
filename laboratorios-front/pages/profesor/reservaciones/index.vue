<template>
	<div>
		<Breadcrumbs id="profesorReservaciones" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="reservacionesLoading" :campos="reservacionesCampos" :listado="reservacionesListado">
			<template v-slot:reseIdGrupo="{ item }">
				<VueVar :grupo="getGrupo(item)" v-slot="{ grupo }">
					<div>
						<div v-if="grupo.grupClave">
							<span class="text-smaller text-muted">Clave Grupo</span>
							<span class="text-primary">{{ grupo.grupClave }}</span>
						</div>
						<div v-if="grupo.asigNombre">
							<span class="text-smaller text-muted">Asignatura</span>
							<span class="text-primary">{{ grupo.asigNombre }}</span>
						</div>
						<div class="text-small" v-if="grupo.asigIdAsignatura">
							<span class="text-smaller text-muted">Clave Asignatura</span>
							{{ grupo.asigIdAsignatura }}
						</div>
						<div class="text-small" v-if="grupo.grupSemestre">
							<span class="text-smaller text-muted">Semestre</span>
							{{ grupo.grupSemestre }}
						</div>
						<div class="text-small" v-if="grupo.grupIdPeriodo">
							<span class="text-smaller text-muted">Periodo</span>
							{{ grupo.grupIdPeriodo }}
						</div>
						<div class="text-small" v-if="grupo.carrNombre">
							<span class="text-smaller text-muted">Carrera</span>
							{{ grupo.carrNombre }}
						</div>
						<div class="text-small" v-if="grupo.grupAlumnosInscritos">
							<span class="text-smaller text-muted">Alumnos Inscritos</span>
							{{ grupo.grupAlumnosInscritos }}
						</div>

						<div v-if="grupo.gruoClave">
							<span class="text-smaller text-muted">Clave Grupo</span>
							<span class="text-primary">{{ grupo.gruoClave }}</span>
						</div>
						<div v-if="grupo.asigNombreP">
							<span class="text-smaller text-muted">Asignatura</span>
							<span class="text-primary">{{ grupo.asigNombreP }}</span>
						</div>
						<div class="text-small" v-if="grupo.asigIdAsignaturaP">
							<span class="text-smaller text-muted">Clave Asignatura</span>
							{{ grupo.asigIdAsignaturaP }}
						</div>
						<div class="text-small" v-if="grupo.gruoIdPeriodo">
							<span class="text-smaller text-muted">Periodo</span>
							{{ grupo.gruoIdPeriodo }}
						</div>
						<div class="text-small" v-if="grupo.coorNombre">
							<span class="text-smaller text-muted">Coordinación</span>
							{{ grupo.coorNombre }}
						</div>
						<div class="text-small" v-if="grupo.gruoAlumnosInscritos">
							<span class="text-smaller text-muted">Alumnos Inscritos</span>
							{{ grupo.gruoAlumnosInscritos }}
						</div>

						<div v-if="grupo.grotClave">
							<span class="text-smaller text-muted">Clave Grupo</span>
							<span class="text-primary">{{ grupo.grotClave }}</span>
						</div>
						<div v-if="grupo.mootNombre">
							<span class="text-smaller text-muted">Módulo</span>
							<span class="text-primary">{{ grupo.mootNombre }}</span>
						</div>
						<div class="text-small" v-if="grupo.mootClave">
							<span class="text-smaller text-muted">Clave Módulo</span>
							{{ grupo.mootClave }}
						</div>
						<div class="text-small" v-if="grupo.grotCupo">
							<span class="text-smaller text-muted">Alumnos Inscritos</span>
							{{ grupo.grotCupo }}
						</div>
					</div>
				</VueVar>
			</template>

			<template v-slot:reseIdLaboratorio="{ item }">
				<VueVar :lab="getLab(item)" v-slot="{ lab }">
					<div>
						<div class="text-primary h6 m-0">{{ lab.saloClave }}</div>
						<div class="text-muted text-small">{{ lab.saloUbicacion }}</div>
						<div class="text-muted text-small">
							Cupo: <span class="font-weight-bolder">{{ lab.saloCupo }}</span>
						</div>
					</div>
				</VueVar>
			</template>

			<template v-slot:reseFecha="{ item }">
				<div>
					<b-icon icon="calendar" variant="muted" class="mr-1"></b-icon>
					{{ $moment(item.reseFecha, 'yyyy-MM-DD').format('DD MMM yy') }}
				</div>
				<div>
					<b-icon icon="clock" variant="muted" class="mr-1"></b-icon>
					{{ formatHorario(item) }}
				</div>
			</template>

			<template v-slot:reseStatus="{ item }">
				<div v-if="item.reseStatus === 'A'">
					<template v-if="isReservaActiva(item)">
						<b-icon icon="check" variant="success"></b-icon>
						<span class="text-success">Activa</span>
					</template>
					<template v-else>
						<b-icon icon="check" variant="primary"></b-icon>
					</template>
				</div>
				<div v-else-if="item.reseStatus === 'C'">
					<b-icon icon="x" variant="danger"></b-icon>
					<span class="text-danger">Cancelada</span>
				</div>
			</template>

      <template v-slot:actions="{ item }">
        <div class="d-flex justify-content-center">
          <!-- Botón cancelar reservación -->
          <b-button
            v-if="item.reseStatus === 'A' && showDeleteButton(item)"
            variant="outline-primary"
            class="border-0"
            size="sm"
            @click="cancelarReservacion(item)"
            v-b-tooltip.hover.top.ds300
            title="Cancelar Reservación"
          >
            <b-icon icon="trash"></b-icon>
          </b-button>

          <!-- Botón descargar PDF -->
          <b-button
            variant="outline-success"
            class="border-0 ml-2"
            size="sm"
            @click="descargarPDF(item)"
            v-b-tooltip.hover.top
            title="Descargar PDF"
          >
            <b-icon icon="download"></b-icon>
          </b-button>
        </div>
      </template>

    </CatalogosListado>
	</div>
</template>

<script src="./index.js"></script>
