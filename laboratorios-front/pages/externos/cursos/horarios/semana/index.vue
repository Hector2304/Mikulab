<template>
	<div>
		<Breadcrumbs id="externosCursosHorariosSemana" />

		<b-card no-body>
			<div v-if="horariosSelectedCurso" class="border-bottom p-2 text-center">
				<span class="text-primary">Curso</span>
				<span class="ml-1 ml-sm-4">{{ horariosSelectedCurso.cuexNombre }}</span>
				<span class="ml-2 ml-sm-5 text-primary">Clave</span>
				<span class="ml-1 ml-sm-4">{{ horariosSelectedCurso.cuexClave }}</span>
				<span class="ml-2 ml-sm-5 text-primary">Alumnos</span>
				<span class="ml-1 ml-sm-4">{{ horariosSelectedCurso.cuexAlumnosInscritos }}</span>
				<template v-if="horariosSelectedCurso.instructorExternoDTO">
					<span class="ml-2 ml-sm-5 text-primary">Instructor</span>
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
			<div class="d-flex flex-row justify-content-center border-bottom">
				<b-form-datepicker
					placeholder="Seleccionar semana..."
					v-model="weekDate"
					v-bind="$options.CalendarLabels['es-MX']"
					:disabled="loading"
					:start-weekday="1"
					locale="es-MX"
					size="sm"
					@input="loadDates"
					:date-disabled-fn="dateDisabled"
					:date-info-fn="highlightWeek"
					button-variant="outline"
					button-only
					hide-header
				></b-form-datepicker>
				<div class="p-2 d-flex flex-row align-items-center justify-content-center">
					<span class="text-primary">Semana</span>
					<span class="ml-1 ml-sm-4">{{ semana }}</span>
				</div>
			</div>
			<div class="d-flex flex-row justify-content-center border-bottom">
				<div class="p-2 d-flex flex-column align-items-center justify-content-center">
					<b-dropdown
						size="sm"
						variant="secondary-outline"
						class="border-0 labs-dropdown"
						toggle-class=" d-flex flex-row flex-wrap align-items-center justify-content-center"
						:disabled="laboratoriosLoading"
					>
						<template #button-content>
							<b-icon icon="building" class="mr-2"></b-icon>

							<template v-if="laboratoriosLoading">
								<b-spinner variant="primary" small></b-spinner>
							</template>
							<template v-else>
								<template v-if="horariosSelectedLab">
									<span class="text-primary">Laboratorio</span>
									<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloClave }}</span>
									<span class="ml-2 ml-sm-4 text-primary">Cupo</span>
									<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloCupo }}</span>
								</template>
								<template v-else>
									<span class="text-primary">Laboratorios</span>
								</template>
							</template>
						</template>

						<b-dropdown-group header="Laboratorios">
							<b-dropdown-item-button
								v-for="(item, i) of laboratorios"
								:key="`mcop-${i}`"
								class="py-1"
								:class="horariosSelectedLab === item ? 'bg-light' : undefined"
								@click="selectLab(item)"
							>
								<div class="text-primary m-0">{{ item.saloClave }}</div>
								<div class="text-muted text-small">{{ item.saloUbicacion }}</div>
								<div class="text-muted text-smaller">
									Cupo: <span class="font-weight-bolder text-small">{{ item.saloCupo }}</span>
								</div>
							</b-dropdown-item-button>
						</b-dropdown-group>
					</b-dropdown>
				</div>
			</div>

			<div v-if="loading" class="d-flex p-4 justify-content-center align-items-center">
				<b-spinner variant="primary"></b-spinner>
			</div>
			<SchedulerWeek
				v-show="!loading && weekDate && horariosSelectedLab"
				ref="schedulerWeek"
				:sch-options="schOptions"
				@default-cell-action="defaultCellAction"
				@undelete-cell="undeleteCell"
				@delete-cell="deleteCell"
			/>
		</b-card>
	</div>
</template>

<script src="./index.js"></script>

<style scoped>
.labs-dropdown /deep/ .dropdown-menu {
	max-height: 500px;
	overflow-y: auto;
}
</style>