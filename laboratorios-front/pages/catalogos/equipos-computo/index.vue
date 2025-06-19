<template>
	<div>
		<Breadcrumbs id="catalogoEquiposComputo" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="equiposLoading" :campos="equiposCampos" :listado="equiposListado">
			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" v-b-modal.add-new-equipo>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="getLaboratorioAsignado(item)"
						v-b-tooltip.hover.top.ds300
						title="Laboratorio asignado"
						><b-icon icon="door-closed"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarEquipo(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="eliminarEquipo(item)"
						v-b-tooltip.hover.top.ds300
						title="Eliminar"
						><b-icon icon="trash"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Agregar equipo -->
		<b-modal
			id="add-new-equipo"
			@shown="focusField('campoAddNombre')"
			@cancel="resetForm"
			@ok="agregarEquipo"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Agregar Equipo de Cómputo</h5></template>

			<form ref="equiposAddForm" @submit.stop.prevent="agregarEquipo">
				<b-form-group label="Nombre" label-for="campoAddNombre">
					<b-form-input
						id="campoAddNombre"
						ref="campoAddNombre"
						v-model.trim="$v.equiposAddForm.nombre.$model"
						:state="validateState('equiposAddForm', 'nombre')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposAddFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Descripción" label-for="campoAddDescripcion">
					<b-form-textarea
						id="campoAddDescripcion"
						ref="campoAddDescripcion"
						v-model.trim="$v.equiposAddForm.descripcion.$model"
						:state="validateState('equiposAddForm', 'descripcion')"
						:disabled="equiposLoading"
						maxlength="1000"
						rows="2"
						max-rows="2"
						required
					></b-form-textarea>

					<b-form-invalid-feedback>{{ equiposAddFormDescripcionMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Número de Inventario" label-for="campoAddNumeroInventario">
					<b-form-input
						id="campoAddNumeroInventario"
						ref="campoAddNumeroInventario"
						v-model.trim="$v.equiposAddForm.numeroInventario.$model"
						:state="validateState('equiposAddForm', 'numeroInventario')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposAddFormNumeroInventarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Estatus" label-for="campoAddStatus">
					<b-form-input
						id="campoAddStatus"
						ref="campoAddStatus"
						v-model.trim="$v.equiposAddForm.status.$model"
						:state="validateState('equiposAddForm', 'status')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposAddFormStatusMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="equiposLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="equiposLoading" @click="ok()">
					<b-spinner v-if="equiposLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar equipo -->
		<b-modal
			id="update-equipo"
			@shown="focusField('campoUpdateNombre')"
			@cancel="resetForm"
			@ok="actualizarEquipo"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Editar Equipo de Cómputo</h5></template>

			<form ref="equiposUpdateForm" @submit.stop.prevent="actualizarEquipo">
				<b-form-group label="Nombre" label-for="campoUpdateNombre">
					<b-form-input
						id="campoUpdateNombre"
						ref="campoUpdateNombre"
						v-model.trim="$v.equiposUpdateForm.nombre.$model"
						:state="validateState('equiposUpdateForm', 'nombre')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposUpdateFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Descripción" label-for="campoUpdateDescripcion">
					<b-form-textarea
						id="campoUpdateDescripcion"
						ref="campoUpdateDescripcion"
						v-model.trim="$v.equiposUpdateForm.descripcion.$model"
						:state="validateState('equiposUpdateForm', 'descripcion')"
						:disabled="equiposLoading"
						maxlength="1000"
						rows="2"
						max-rows="2"
						required
					></b-form-textarea>

					<b-form-invalid-feedback>{{ equiposUpdateFormDescripcionMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Número de Inventario" label-for="campoUpdateNumeroInventario">
					<b-form-input
						id="campoUpdateNumeroInventario"
						ref="campoUpdateNumeroInventario"
						v-model.trim="$v.equiposUpdateForm.numeroInventario.$model"
						:state="validateState('equiposUpdateForm', 'numeroInventario')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposUpdateFormNumeroInventarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Estatus" label-for="campoUpdateStatus">
					<b-form-input
						id="campoUpdateStatus"
						ref="campoUpdateStatus"
						v-model.trim="$v.equiposUpdateForm.status.$model"
						:state="validateState('equiposUpdateForm', 'status')"
						:disabled="equiposLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ equiposUpdateFormStatusMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="equiposLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="equiposLoading" @click="ok()">
					<b-spinner v-if="equiposLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar equipo -->
		<b-modal id="lab-asignado-modal" no-close-on-esc no-close-on-backdrop hide-header-close scrollable centered>
			<template #modal-header><h5 class="text-primary">Laboratorio asignado</h5></template>

			<template v-if="equiposLabAsignadoLoading">
				<div class="text-secondary text-small text-center">
					<b-spinner variant="primary"></b-spinner>
				</div>
			</template>

			<template v-else>
				<div v-if="equiposLabAsignado">
					<b-row>
						<b-col cols="12" sm="3" class="text-primary">Clave</b-col>
						<b-col cols="12" sm="9">{{ equiposLabAsignado.saloClave }}</b-col>
					</b-row>

					<b-row class="mt-2">
						<b-col cols="12" sm="3" class="text-primary">Ubicación</b-col>
						<b-col cols="12" sm="9">{{ equiposLabAsignado.saloUbicacion }}</b-col>
					</b-row>

					<b-row class="mt-2">
						<b-col cols="12" sm="3" class="text-primary">Cupo</b-col>
						<b-col cols="12" sm="9">{{ equiposLabAsignado.saloCupo }}</b-col>
					</b-row>
				</div>

				<div v-else class="text-secondary text-small text-center">Sin laboratorio asignado</div>
			</template>

			<template #modal-footer="{ cancel }">
				<b-button size="sm" variant="secondary" @click="cancel()" :disabled="equiposLabAsignadoLoading">
					Cerrar
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>