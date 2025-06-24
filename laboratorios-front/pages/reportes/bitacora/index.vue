<template>
	<div>
		<Breadcrumbs id="reportesBitacora" />

		<CatalogosListado :busy="loading" :campos="campos" :listado="listado">
			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" @click="openCrearReporte">
					<b-icon icon="plus"></b-icon> Crear reporte del día
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						v-if="$moment(item.bitaFecha).isSame(today)"
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="goToEdicion(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
					>
						<b-icon icon="pencil"></b-icon>
					</b-button>
					<b-button
						v-else
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="goToEdicion(item)"
						v-b-tooltip.hover.top.ds300
						title="Consultar"
					>
						<b-icon icon="eye"></b-icon>
					</b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Crear reporte del día -->
		<b-modal
			id="crear-reporte-modal"
			@ok="crearReporte"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Crear reporte del día</h5></template>

			<div class="mb-1">
				<span class="text-primary mr-sm-4">Fecha</span>
				<span class="ml-5">{{ $moment.tz('America/Mexico_City').format('DD MMMM yy') }}</span>
			</div>

			<b-form-group label="Tipo" label-for="campoTipo" label-cols="3" label-class="text-primary">
				<b-form-select
					id="campoTipo"
					ref="campoTipo"
					v-model="$v.crearForm.tipo.$model"
					:state="validateState('crearForm', 'tipo')"
					:disabled="loading"
					:options="tipoOptions"
				>
				</b-form-select>

				<b-form-invalid-feedback>{{ crearFormTipoMsgs }}</b-form-invalid-feedback>
			</b-form-group>

			<b-form-group label="Laboratorios" label-for="campoTipoLab" label-cols="3" label-class="text-primary">
				<b-form-select
					id="campoTipoLab"
					ref="campoTipoLab"
					v-model="$v.crearForm.tipoLab.$model"
					:state="validateState('crearForm', 'tipoLab')"
					:disabled="loading"
					:options="tipoLabOptions"
				>
				</b-form-select>

				<b-form-invalid-feedback>{{ crearFormTipoLabMsgs }}</b-form-invalid-feedback>
			</b-form-group>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="loading" @click="cancel()"> Cancelar </b-button>
				<b-button size="sm" variant="primary" :disabled="loading" @click="ok()">
					<b-spinner v-if="loading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>