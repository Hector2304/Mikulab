<template>
	<div>
		<Breadcrumbs id="catalogoSoftware" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<!-- Software -->
		<CatalogosListado :busy="softwareLoading" :campos="softwareCampos" :listado="softwareListado">
			<template v-slot:top-title>
				<h6 class="mr-sm-3 mb-0 text-primary">Software</h6>
			</template>

			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" v-b-modal.add-new-software>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarSofware(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="eliminarSoftware(item)"
						v-b-tooltip.hover.top.ds300
						title="Eliminar"
						><b-icon icon="trash"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Agregar software -->
		<b-modal
			id="add-new-software"
			@shown="focusField('campoAddSoftware')"
			@cancel="resetFormSoftware"
			@ok="agregarSoftware"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Agregar Software</h5></template>

			<form ref="softwareAddForm" @submit.stop.prevent="agregarSoftware">
				<b-form-group label="Software" label-for="campoAddSoftware">
					<b-form-input
						id="campoAddSoftware"
						ref="campoAddSoftware"
						v-model.trim="$v.softwareAddForm.software.$model"
						:state="validateState('softwareAddForm', 'software')"
						:disabled="softwareLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ softwareAddFormMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="softwareLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="softwareLoading" @click="ok()">
					<b-spinner v-if="softwareLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar software -->
		<b-modal
			id="update-software"
			@shown="focusField('campoUpdateSoftware')"
			@cancel="resetFormSoftware"
			@ok="actualizarSoftware"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Editar Software</h5></template>

			<form ref="softwareUpdateForm" @submit.stop.prevent="actualizarSoftware">
				<b-form-group label="Software" label-for="campoUpdateSoftware">
					<b-form-input
						id="campoUpdateSoftware"
						ref="campoUpdateSoftware"
						v-model.trim="$v.softwareUpdateForm.software.$model"
						:state="validateState('softwareUpdateForm', 'software')"
						:disabled="softwareLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ softwareUpdateFormMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="softwareLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="softwareLoading" @click="ok()">
					<b-spinner v-if="softwareLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>