<template>
	<div>
		<Breadcrumbs id="reportesProgramadosDiaEdicion" />

		<b-card no-body>
			<div class="border-bottom p-2 text-center">
				<span class="text-primary">Grupos programados en laboratorios de cómputo</span>

				<span class="ml-1 ml-sm-4">{{ formattedDay }}</span>

				<b-button
					variant="outline-primary"
					class="border-0 ml-1 ml-sm-4"
					size="sm"
					@click="openHistorico"
					v-b-tooltip.hover.top.ds300
					title="Histórico"
				>
					<b-icon icon="card-list"></b-icon>
				</b-button>

				<b-button
					variant="outline-primary"
					class="border-0 ml-1 ml-sm-4"
					size="sm"
					@click="descargarExcel"
					v-b-tooltip.hover.top.ds300
					title="Descargar Excel"
				>
					<b-icon icon="file-earmark-spreadsheet"></b-icon>
				</b-button>
			</div>
			<div v-if="loading" class="d-flex p-4 justify-content-center align-items-center">
				<b-spinner variant="primary"></b-spinner>
			</div>
			<b-table-simple v-else class="m-0" stacked="sm" small responsive>
				<b-thead>
					<b-tr>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0
							"
							nowrap
						>
							Día
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Lab
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Hora
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Asignatura/Grupo/Profesor
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Horario de apertura
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Asistencia
						</b-th>
						<b-th
							class="
								text-small text-center
								align-middle
								flex-no-wrap
								px-3
								py-1
								border-top-0 border-bottom-0 border-left
							"
							nowrap
						>
							Observaciones
						</b-th>
					</b-tr>
				</b-thead>
				<b-tbody>
					<b-tr v-for="(item, i) of listado" :key="`tr-${i}`">
						<b-td class="text-center align-middle px-3 py-1" stacked-heading="Día">{{ weekDay }}</b-td>
						<b-td class="text-center align-middle px-3 py-1 border-left text-nowrap" stacked-heading="Lab">
							{{ getLab(item.repdIdLaboratorio).saloClave }}
						</b-td>
						<b-td
							class="text-center align-middle px-3 py-1 border-left text-nowrap"
							stacked-heading="Hora"
							>{{ getHora(item.repdIdHorario) }}</b-td
						>
						<b-td
							class="text-left align-middle px-3 py-1 border-left"
							stacked-heading="Asignatura / Grupo / Profesor"
						>
							<VueVar :gr="getGrupo(item)" v-slot="{ gr }">
								<ReporteGrupoExterno v-if="item.repdTipoGrupo === 'E'" :info="gr" />
								<ReporteGrupoLicenciatura v-else-if="item.repdTipoGrupo === 'L'" :info="gr" />
								<ReporteGrupoPosgrado v-else-if="item.repdTipoGrupo === 'P'" :info="gr" />
								<ReporteGrupoTitulacion v-else-if="item.repdTipoGrupo === 'T'" :info="gr" />
							</VueVar>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Hora de apertura">
							<b-form-timepicker
								v-if="isEditable"
								v-model="item.repdHoraApertura"
								v-bind="$options.TimeLabels['es-MX']"
								locale="es-MX"
								class="border-0"
								size="sm"
								:hour12="false"
							></b-form-timepicker>
							<template v-else>{{ $moment(item.repdHoraApertura, 'HH:mm:ss').format('HH:mm') }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Asistencia">
							<b-form-checkbox v-if="isEditable" v-model="item.repdAsistencia" switch> </b-form-checkbox>
							<span v-else-if="item.repdAsistencia" class="text-primary">&check;</span>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Observaciones">
							<b-button
								variant="outline-primary"
								class="border-0"
								size="sm"
								@click="openObservaciones(item)"
							>
								<div class="d-flex justify-content-center align-items-center">
									<span class="mr-2">{{ countObs(item.repdIdDetalle) }}</span>
									<b-icon icon="chat-square-text" scale="1"></b-icon>
								</div>
							</b-button>
						</b-td>
					</b-tr>
				</b-tbody>
			</b-table-simple>
			<div v-if="isEditable && !loading" class="p-3 w-100 d-flex justify-content-center border-top">
				<b-button class="mx-auto" variant="outline-primary" @click="guardarCambios">Guardar cambios</b-button>
			</div>
		</b-card>

		<ReporteObservaciones
			ref="reporteObservaciones"
			:options="obsOptions"
			:editable="isEditable"
			:detalle-id="detalleSelected ? detalleSelected.repdIdDetalle : undefined"
			:observaciones="detalleSelected ? observaciones[`${detalleSelected.repdIdDetalle}`] : undefined"
			@alta="altaCallback"
			@baja="bajaCallback"
		>
			<template v-if="detalleSelected" v-slot:info>
				<span class="text-primary">Laboratorio</span>
				<span class="ml-1 ml-sm-4">{{ getLab(detalleSelected.repdIdLaboratorio).saloClave }}</span>
				<span class="ml-1 ml-sm-4">{{ getLab(detalleSelected.repdIdLaboratorio).saloUbicacion }}</span>
				<hr />
			</template>
		</ReporteObservaciones>

		<ReporteHistorico ref="reporteHistorico" :options="historicoOptions"></ReporteHistorico>
	</div>
</template>

<script src="./index.js"></script>
