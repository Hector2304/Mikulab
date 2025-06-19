<template>
	<div>
		<Breadcrumbs id="externosInstructores" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="instructoresLoading" :campos="instructoresCampos" :listado="instructoresListado">
			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" v-b-modal.add-new-instructor>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarInstructor(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="abrirAsignarCursos(item)"
						v-b-tooltip.hover.top.ds300
						title="Cursos"
						><b-icon icon="journal-bookmark"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Agregar instructor -->
		<b-modal
			id="add-new-instructor"
			@shown="focusField('campoAddNombre')"
			@cancel="resetForm"
			@ok="agregarInstructor"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Agregar Instructor</h5></template>

			<form ref="cursosAddForm" @submit.stop.prevent="agregarInstructor">
				<b-form-group label="Nombre" label-for="campoAddNombre">
					<b-form-input
						id="campoAddNombre"
						ref="campoAddNombre"
						v-model.trim="$v.instructoresAddForm.nombre.$model"
						:state="validateState('instructoresAddForm', 'nombre')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresAddFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Paterno" label-for="campoAddApaterno">
					<b-form-input
						id="campoAddApaterno"
						ref="campoAddApaterno"
						v-model.trim="$v.instructoresAddForm.aPaterno.$model"
						:state="validateState('instructoresAddForm', 'aPaterno')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresAddFormAPaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Materno" label-for="campoAddAmaterno">
					<b-form-input
						id="campoAddAmaterno"
						ref="campoAddAmaterno"
						v-model.trim="$v.instructoresAddForm.aMaterno.$model"
						:state="validateState('instructoresAddForm', 'aMaterno')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresAddFormAMaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Teléfono" label-for="campoAddTelefono">
					<b-form-input
						id="campoAddTelefono"
						ref="campoAddTelefono"
						v-model.trim="$v.instructoresAddForm.telefono.$model"
						:state="validateState('instructoresAddForm', 'telefono')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresAddFormTelefonoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Correo" label-for="campoAddCorreo">
					<b-form-input
						id="campoAddCorreo"
						ref="campoAddCorreo"
						v-model.trim="$v.instructoresAddForm.correo.$model"
						:state="validateState('instructoresAddForm', 'correo')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresAddFormCorreoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="instructoresLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="instructoresLoading" @click="ok()">
					<b-spinner v-if="instructoresLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar instructor -->
		<b-modal
			id="update-instructor"
			@shown="focusField('campoUpdateNombre')"
			@cancel="resetForm"
			@ok="actualizarInstructor"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Editar Instructor</h5></template>

			<form ref="instructoresUpdateForm" @submit.stop.prevent="actualizarInstructor">
				<b-form-group label="Nombre" label-for="campoUpdateNombre">
					<b-form-input
						id="campoUpdateNombre"
						ref="campoUpdateNombre"
						v-model.trim="$v.instructoresUpdateForm.nombre.$model"
						:state="validateState('instructoresUpdateForm', 'nombre')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresUpdateFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Paterno" label-for="campoUpdateApaterno">
					<b-form-input
						id="campoUpdateApaterno"
						ref="campoUpdateApaterno"
						v-model.trim="$v.instructoresUpdateForm.aPaterno.$model"
						:state="validateState('instructoresUpdateForm', 'aPaterno')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresUpdateFormAPaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Materno" label-for="campoUpdateAmaterno">
					<b-form-input
						id="campoUpdateAmaterno"
						ref="campoUpdateAmaterno"
						v-model.trim="$v.instructoresUpdateForm.aMaterno.$model"
						:state="validateState('instructoresUpdateForm', 'aMaterno')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresUpdateFormAMaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Teléfono" label-for="campoUpdateTelefono">
					<b-form-input
						id="campoUpdateTelefono"
						ref="campoUpdateTelefono"
						v-model.trim="$v.instructoresUpdateForm.telefono.$model"
						:state="validateState('instructoresUpdateForm', 'telefono')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresUpdateFormTelefonoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Correo" label-for="campoUpdateCorreo">
					<b-form-input
						id="campoUpdateCorreo"
						ref="campoUpdateCorreo"
						v-model.trim="$v.instructoresUpdateForm.correo.$model"
						:state="validateState('instructoresUpdateForm', 'correo')"
						:disabled="instructoresLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ instructoresUpdateFormCorreoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="instructoresLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="instructoresLoading" @click="ok()">
					<b-spinner v-if="instructoresLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Asignar cursos -->
		<b-modal
			id="asign-cursos"
			@ok="guardarAsignacionCursos"
			size="xl"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Cursos</h5></template>

			<div v-if="instructoresEditing">
				<span class="text-primary">Instructor</span>
				<span class="ml-1 ml-sm-4">
					{{ instructoresEditing.inexNombre }}
					{{ instructoresEditing.inexApaterno }}
					{{ instructoresEditing.inexAmaterno }}
				</span>
			</div>

			<hr />

			<template v-if="!cursosEditing">
				<CatalogosListado
					id="catalogosListadoCursos"
					key="catalogosListadoCursos"
					ref="catalogosListadoCursos"
					:busy="cursosInstructorLoading"
					:campos="cursosCampos"
					:listado="cursosInstructorListado"
				>
					<template v-slot:top-buttons>
						<b-button
							variant="outline-primary"
							size="sm"
							class="ml-3"
							:disabled="cursosInstructorLoading"
							@click="startEditingCursos"
						>
							<b-icon icon="pencil"></b-icon> Editar
						</b-button>
					</template>
				</CatalogosListado>
			</template>

			<template v-else>
				<CatalogosListado
					id="catalogosListadocursosEditing"
					key="catalogosListadocursosEditing"
					ref="catalogosListadocursosEditing"
					:busy="cursosEditingLoading"
					:campos="[
						{
							key: 'selected',
							label: '',
							class: 'text-center align-middle',
							slotable: true
						},
						...cursosCampos
					]"
					:listado="cursosEditingFullList"
					:tbody-tr-class="tbodyTrClass"
					@row-clicked="rowClicked"
					selectable
					select-mode="multi"
				>
					<template v-slot:top-title>
						<span class="text-primary h6 m-0 mr-2">Seleccionar Cursos</span>
					</template>

					<template v-slot:selected="{ item }">
						<span class="mx-1" v-html="item.selected ? '&check;' : '&nbsp;'"></span>
					</template>
				</CatalogosListado>
			</template>

			<template #modal-footer="{ ok, cancel }">
				<template v-if="cursosEditing">
					<b-button
						size="sm"
						variant="secondary"
						:disabled="cursosInstructorLoading || cursosEditingLoading"
						@click="cancelEditingCursos"
					>
						Cancelar
					</b-button>
					<b-button
						size="sm"
						variant="primary"
						:disabled="cursosInstructorLoading || cursosEditingLoading"
						@click="ok()"
					>
						<b-spinner v-if="cursosInstructorLoading || cursosEditingLoading" small></b-spinner>
						<span v-else>Confirmar</span>
					</b-button>
				</template>

				<template v-else>
					<b-button
						size="sm"
						variant="secondary"
						:disabled="cursosInstructorLoading || cursosEditingLoading"
						@click="cancel()"
					>
						Cerrar
					</b-button>
				</template>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>