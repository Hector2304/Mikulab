<template>
	<div>
		<Breadcrumbs id="catalogoLaboratoriosHorariosBloquear" />

		<b-card no-body>
			<div v-if="horariosSelectedLab" class="border-bottom p-2 text-center">
				<span class="text-primary">Laboratorio</span>
				<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloClave }}</span>
				<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloUbicacion }}</span>
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
					@input="loadBloqueo"
					:date-disabled-fn="dateDisabled"
					:date-info-fn="highlightWeek"
					button-variant="outline"
					button-only
					hide-header
				></b-form-datepicker>
				<div class="p-2">
					<span class="text-primary">Semana</span>
					<span class="ml-1 ml-sm-4">{{ semana }}</span>
				</div>
			</div>

			<div v-if="loading" class="d-flex p-4 justify-content-center align-items-center">
				<b-spinner variant="primary"></b-spinner>
			</div>
			<SchedulerWeek
				v-show="!loading && weekDate"
				ref="schedulerWeek"
				:sch-options="schOptions"
				@default-cell-action="defaultCellAction"
				@undelete-cell="undeleteCell"
				@delete-cell="deleteCell"
			/>
		</b-card>

		<!-- Agregar bloq -->
		<b-modal
			id="add-new-bloq"
			@shown="focusField('campoMotivo')"
			@ok="agregarBloq"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Bloquear horario</h5></template>

			<div v-if="bloqForm && bloqForm.date && bloqForm.day && bloqForm.hour">
				<div>
					<span class="text-primary">Fecha</span>
					<span class="ml-1 ml-sm-4"
						>{{ bloqForm.day.text }}, &nbsp;&nbsp;
						{{ $moment(bloqForm.date, 'yyyy-MM-DD').format('DD MMM yy') }}</span
					>
				</div>
				<div>
					<span class="text-primary">Horario</span>
					<span class="ml-1 ml-sm-4">{{ bloqForm.hour.text }}</span>
				</div>
				<div>
					<span class="text-primary">Laboratorio</span>
					<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloClave }}</span>
				</div>
			</div>

			<hr />

			<form ref="motivoForm" @submit.stop.prevent="agregarBloq">
				<b-form-group label="Motivo" label-for="campoMotivo">
					<b-form-input
						placeholder="(Opcional)"
						id="campoMotivo"
						ref="campoMotivo"
						v-model.trim="$v.bloqForm.motivo.$model"
						:state="validateState('bloqForm', 'motivo')"
						:disabled="blocking"
						maxlength="255"
						required
					></b-form-input>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="blocking" @click="cancel()"> Cancelar </b-button>
				<b-button size="sm" variant="primary" :disabled="blocking" @click="ok()">
					<b-spinner v-if="blocking" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>