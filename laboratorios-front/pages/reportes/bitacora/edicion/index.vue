<template>
	<div>
		<Breadcrumbs id="reportesBitacoraEdicion" />

		<b-card no-body>
			<div class="border-bottom p-2 text-center">
				<span class="text-primary"
					>Bitácora de {{ paramBitacora.bitaTipo === 'C' ? 'Cierre' : 'Apertura' }}
					{{ paramBitacora.bitaTipoLab }} de laboratorios</span
				>

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
							Monitor
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
							CPU
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
							Teclado
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
							Mouse
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
							Video Proyector
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
							Cable D.Port
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
							Control cañón
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
							Control aire
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
							Hora de apertura
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
							Hora de cierre
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
							Vigilante
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
						<b-td class="text-center align-middle px-3 py-1 text-nowrap" stacked-heading="Lab">
							{{ getLab(item.bideIdLaboratorio).saloClave }}
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Monitor">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideMonitor"
							></b-form-spinbutton>
							<template v-else>{{ item.bideMonitor }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="CPU">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideCpu"
							></b-form-spinbutton>
							<template v-else>{{ item.bideCpu }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Teclado">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideTeclado"
							></b-form-spinbutton>
							<template v-else>{{ item.bideTeclado }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Mouse">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideMouse"
							></b-form-spinbutton>
							<template v-else>{{ item.bideMouse }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Video Proyector">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideVideoProyector"
							></b-form-spinbutton>
							<template v-else>{{ item.bideVideoProyector }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Cable D.Port">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideCableDport"
							></b-form-spinbutton>
							<template v-else>{{ item.bideCableDport }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Control cañon">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideControlCanon"
							></b-form-spinbutton>
							<template v-else>{{ item.bideControlCanon }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Control aire">
							<b-form-spinbutton
								v-if="isEditable"
								size="sm"
								class="border-0"
								v-model="item.bideControlAire"
							></b-form-spinbutton>
							<template v-else>{{ item.bideControlAire }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Hora de apertura">
							<b-form-timepicker
								v-if="isEditable"
								v-model="item.bideHoraApertura"
								v-bind="$options.TimeLabels['es-MX']"
								locale="es-MX"
								class="border-0"
								size="sm"
								:hour12="false"
							></b-form-timepicker>
							<template v-else>{{ $moment(item.bideHoraApertura, 'HH:mm:ss').format('HH:mm') }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Hora de cierre">
							<b-form-timepicker
								v-if="isEditable"
								v-model="item.bideHoraCierre"
								v-bind="$options.TimeLabels['es-MX']"
								locale="es-MX"
								class="border-0"
								size="sm"
								:hour12="false"
							></b-form-timepicker>
							<template v-else>{{ $moment(item.bideHoraCierre, 'HH:mm:ss').format('HH:mm') }}</template>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Vigilante">
							<b-form-select
								v-model="item.bideVigilante"
								:disabled="!isEditable"
								:options="vigilantes"
								size="sm"
							>
							</b-form-select>
						</b-td>
						<b-td class="text-center align-middle border-left" stacked-heading="Observaciones">
							<b-button
								variant="outline-primary"
								class="border-0"
								size="sm"
								@click="openObservaciones(item)"
							>
								<div class="d-flex justify-content-center align-items-center">
									<span class="mr-2">{{ countObs(item.bideIdDetalle) }}</span>
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
			:detalle-id="detalleSelected ? detalleSelected.bideIdDetalle : undefined"
			:observaciones="detalleSelected ? observaciones[`${detalleSelected.bideIdDetalle}`] : undefined"
			@alta="altaCallback"
			@baja="bajaCallback"
		>
			<template v-if="detalleSelected" v-slot:info>
				<span class="text-primary">Laboratorio</span>
				<span class="ml-1 ml-sm-4">{{ getLab(detalleSelected.bideIdLaboratorio).saloClave }}</span>
				<span class="ml-1 ml-sm-4">{{ getLab(detalleSelected.bideIdLaboratorio).saloUbicacion }}</span>
				<hr />
			</template>
		</ReporteObservaciones>

		<ReporteHistorico ref="reporteHistorico" :options="historicoOptions"></ReporteHistorico>
	</div>
</template>

<script src="./index.js"></script>
