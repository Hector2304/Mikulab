<template>
	<div>
		<Breadcrumbs id="profesorReserva" />

		<ConfirmationPrompt ref="confirmationPrompt" />
    <b-overlay :show="cargandoTodo" spinner-small>

		<b-card>
			<!-- Grupos -->
			<h5 class="text-primary cursor-pointer" @click="gruposVisibles = !gruposVisibles">
				Grupos
				<b-icon
					:icon="gruposVisibles ? 'chevron-down' : 'chevron-left'"
					variant="secondary"
					font-scale="0.5"
					class="mb-1 ml-1"
				></b-icon>

				<ReservaProfesorGrupoCollapseTitle v-if="grupoSelected" :grupo="grupoSelected" />
			</h5>

			<b-collapse v-model="gruposVisibles">
				<b-form-row>
					<b-col class="mb-2" cols="12" lg="12" xl="4">
						<CatalogosListado
							style="height: 100%"
							table-id="reservaListadoGrupos"
							key="reservaListadoGrupos"
							ref="reservaListadoGrupos"
							:busy="gruposLoading"
							:campos="gruposCampos"
							:listado="grupos"
							:tbody-tr-class="tbodyTrClass"
							@row-clicked="seleccionarGrupo"
							thead-class="text-small"
							selectable
							hide-search
							class="mt-3"
						>
							<template v-slot:top-title>
								<span class="text-primary h6 m-0 mr-2">Licenciatura</span>
							</template>

							<template v-slot:selected="{ item }">
								<span class="mx-1" v-html="item.selected ? '&check;' : '&nbsp;'"></span>
							</template>
						</CatalogosListado>
					</b-col>

					<b-col class="mb-2" cols="12" lg="6" xl="4">
						<CatalogosListado
							style="height: 100%"
							table-id="reservaListadoGruposP"
							key="reservaListadoGruposP"
							ref="reservaListadoGruposP"
							:busy="gruposLoading"
							:campos="gruposPCampos"
							:listado="gruposP"
							:tbody-tr-class="tbodyTrClass"
							@row-clicked="seleccionarGrupo"
							thead-class="text-small"
							selectable
							hide-search
							class="mt-3"
						>
							<template v-slot:top-title>
								<span class="text-primary h6 m-0 mr-2">Posgrado</span>
							</template>

							<template v-slot:selected="{ item }">
								<span class="mx-1" v-html="item.selected ? '&check;' : '&nbsp;'"></span>
							</template>
						</CatalogosListado>
					</b-col>

					<b-col class="mb-2" cols="12" lg="6" xl="4">
						<CatalogosListado
							style="height: 100%"
							table-id="reservaListadoGruposOT"
							key="reservaListadoGruposOT"
							ref="reservaListadoGruposOT"
							:busy="gruposLoading"
							:campos="gruposOTCampos"
							:listado="gruposOT"
							:tbody-tr-class="tbodyTrClass"
							@row-clicked="seleccionarGrupo"
							thead-class="text-small"
							selectable
							hide-search
							class="mt-3"
						>
							<template v-slot:top-title>
								<span class="text-primary h6 m-0 mr-2">Opción Titulación</span>
							</template>

							<template v-slot:selected="{ item }">
								<span class="mx-1" v-html="item.selected ? '&check;' : '&nbsp;'"></span>
							</template>
						</CatalogosListado>
					</b-col>
				</b-form-row>
			</b-collapse>

			<hr class="my-4" />

			<!-- Laboratorios -->
			<h5 class="text-primary cursor-pointer" @click="laboratoriosVisibles = !laboratoriosVisibles">
				Laboratorios
				<b-icon
					:icon="laboratoriosVisibles ? 'chevron-down' : 'chevron-left'"
					variant="secondary"
					font-scale="0.5"
					class="mb-1 ml-1"
				></b-icon>

				<ReservaProfesorLabCollapseTitle
					v-if="laboratorioSelected && selectedHour && selectedDate"
					:lab="laboratorioSelected"
					:date="selectedDate"
					:hour="selectedHour"
				/>
			</h5>

      <b-collapse v-model="laboratoriosVisibles">
        <div v-if="!grupoSelected" class="text-center text-muted py-4">
          <h6 class="text-secondary font-italic">
            Por favor, elija un grupo para mostrar los laboratorios disponibles.
          </h6>
        </div>

        <CatalogosListado
          v-else
          table-id="reservaListadoLaboratorios"
          key="reservaListadoLaboratorios"
          ref="reservaListadoLaboratorios"
          :busy="laboratoriosLoading"
          :campos="laboratoriosCampos"
          :listado="laboratoriosFiltered"
          :tbody-tr-class="tbodyTrClass"
          thead-class="text-small"
          :hover="false"
          hide-search
        >
          <template v-slot:top-title>
            <div class="d-flex flex-row align-items-center">
              <span class="text-primary text-small m-0 mx-2">Fecha</span>
              <b-form-datepicker
                v-model="selectedDate"
                v-bind="$options.CalendarLabels['es-MX']"
                placeholder="Seleccione una fecha de reserva"
                :start-weekday="1"
                :date-disabled-fn="dateDisabled"
                @input="loadDisponibilidad"
                locale="es-MX"
                size="sm"
              ></b-form-datepicker>
            </div>
            <div class="d-flex flex-row align-items-center">
              <span class="text-primary text-small m-0 ml-4 mr-2">Software</span>
              <ComboboxMultiple
                v-if="softwareListado.length > 0"
                v-model="softwareSeleccionado"
                label="Filtrar laboratorios por software"
                item-label-key="softNombre"
                :items="softwareListado"
              />
            </div>
          </template>

          <!-- Slots lab, software y horario se mantienen igual -->
          <template v-slot:lab="{ item }">
            <div class="text-primary h6 m-0">{{ item.saloClave }}</div>
            <div class="text-muted">{{ item.saloUbicacion }}</div>
            <div class="text-muted">
              Cupo: <span class="font-weight-bolder">{{ item.saloCupo }}</span>
            </div>
          </template>

          <template v-slot:software="{ item }">
            <div class="d-flex flex-row justify-content-center flex-wrap">
              <ComboboxChip
                v-for="(sw, i) of asocLabSoft(item)"
                :key="`cbc-${i}`"
                item-label-key="softNombre"
                :item="sw"
              />
            </div>
          </template>

          <template v-slot:horario="{ item }">
            <div v-if="selectedDate && (disponibilidadLoading || !disponibilidad)" class="d-flex justify-content-center align-items-center py-3">
              <b-spinner small variant="primary" label="Cargando" />
              <span class="ml-2 text-muted">Cargando horarios disponibles...</span>
            </div>

            <ReservaHoraDia
              v-else-if="selectedDate"
              :tamanho-seleccion="numHorasSeleccionables"
              @select="(hr) => selectHora(hr, item)"
              :disponibilidad="disponibilidad"
              :is-sabado="isSabado"
              :lab-id="`${item.saloIdSalon}`"
              :id="`ReservaHoraDia${item.saloIdSalon}`"
              :key="`ReservaHoraDia${item.saloIdSalon}`"
              :ref="`ReservaHoraDia${item.saloIdSalon}`"
            />
          </template>


        </CatalogosListado>
      </b-collapse>


      <hr class="my-4" />

			<!-- Resumen -->
			<h5 class="text-primary cursor-pointer" @click="resumenVisible = !resumenVisible">
				Confirmar
				<b-icon
					:icon="resumenVisible ? 'chevron-down' : 'chevron-left'"
					variant="secondary"
					font-scale="0.5"
					class="mb-1"
				></b-icon>
			</h5>

			<b-collapse v-model="resumenVisible">
				<div class="d-flex flex-row d-flex justify-content-between flex-wrap">
					<div v-if="grupoSelected" class="p-3">
						<div v-if="grupoSelected.grupClave">
							<span class="text-small text-muted">Clave Grupo</span>
							<b>{{ grupoSelected.grupClave }}</b>
						</div>
						<div v-if="grupoSelected.asigIdAsignatura">
							<span class="text-small text-muted">Clave Asignatura</span>
							<b>{{ grupoSelected.asigIdAsignatura }}</b>
						</div>
						<div v-if="grupoSelected.asigNombre">
							<span class="text-small text-muted">Asignatura</span>
							<b>{{ grupoSelected.asigNombre }}</b>
						</div>
						<div v-if="grupoSelected.grupSemestre">
							<span class="text-small text-muted">Semestre</span>
							<b>{{ grupoSelected.grupSemestre }}</b>
						</div>
						<div v-if="grupoSelected.grupIdPeriodo">
							<span class="text-small text-muted">Periodo</span>
							<b>{{ grupoSelected.grupIdPeriodo }}</b>
						</div>
						<div v-if="grupoSelected.carrNombre">
							<span class="text-small text-muted">Carrera</span>
							<b>{{ grupoSelected.carrNombre }}</b>
						</div>
						<div v-if="grupoSelected.grupAlumnosInscritos">
							<span class="text-small text-muted">Alumnos Inscritos</span>
							<b>{{ grupoSelected.grupAlumnosInscritos }}</b>
						</div>
						<div v-if="grupoSelected.gruoClave">
							<span class="text-small text-muted">Clave Grupo</span>
							<b>{{ grupoSelected.gruoClave }}</b>
						</div>
						<div v-if="grupoSelected.asigIdAsignaturaP">
							<span class="text-small text-muted">Clave Asignatura</span>
							<b>{{ grupoSelected.asigIdAsignaturaP }}</b>
						</div>
						<div v-if="grupoSelected.asigNombreP">
							<span class="text-small text-muted">Asignatura</span>
							<b>{{ grupoSelected.asigNombreP }}</b>
						</div>
						<div v-if="grupoSelected.gruoIdPeriodo">
							<span class="text-small text-muted">Periodo</span>
							<b>{{ grupoSelected.gruoIdPeriodo }}</b>
						</div>
						<div v-if="grupoSelected.coorNombre">
							<span class="text-small text-muted">Coordinación</span>
							<b>{{ grupoSelected.coorNombre }}</b>
						</div>
						<div v-if="grupoSelected.gruoAlumnosInscritos">
							<span class="text-small text-muted">Alumnos Inscritos</span>
							<b>{{ grupoSelected.gruoAlumnosInscritos }}</b>
						</div>
						<div v-if="grupoSelected.grotClave">
							<span class="text-small text-muted">Clave Grupo</span>
							<b>{{ grupoSelected.grotClave }}</b>
						</div>
						<div v-if="grupoSelected.mootClave">
							<span class="text-small text-muted">Clave Módulo</span>
							<b>{{ grupoSelected.mootClave }}</b>
						</div>
						<div v-if="grupoSelected.mootNombre">
							<span class="text-small text-muted">Módulo</span>
							<b>{{ grupoSelected.mootNombre }}</b>
						</div>
						<div v-if="grupoSelected.grotCupo">
							<span class="text-small text-muted">Alumnos Inscritos</span>
							<b>{{ grupoSelected.grotCupo }}</b>
						</div>
					</div>
					<div v-if="laboratorioSelected" class="p-3">
						<div>
							<span class="text-small text-muted">Laboratorio</span>
							<b>{{ laboratorioSelected.saloClave }}</b>
						</div>
						<div>
							<span class="text-small text-muted">Ubicación</span>
							<b>{{ laboratorioSelected.saloUbicacion }}</b>
						</div>
						<div>
							<span class="text-small text-muted">Cupo</span> <b>{{ laboratorioSelected.saloCupo }}</b>
						</div>
					</div>
					<div class="p-3">
						<div v-if="selectedDate">
							<span class="text-small text-muted">Fecha</span>
							<b>{{ $moment(selectedDate, 'yyyy-MM-DD').format('DD MMM yy') }}</b>
						</div>
						<div v-if="selectedHour">
							<span class="text-small text-muted">Hora</span>
							<b>{{ selectedHour.hora }}:00 - {{ selectedHourTo }}:00</b>
						</div>
					</div>
					<div class="m-1 w-100 d-flex justify-content-center">
						<b-button class="mx-auto" variant="outline-primary" @click="confirmarReservar">Confirmar y Reservar</b-button>
					</div>
				</div>
			</b-collapse>
		</b-card>
    </b-overlay>

  </div>
</template>

<script src="./index.js"></script>

