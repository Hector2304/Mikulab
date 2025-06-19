<template>
	<div>
		<Breadcrumbs id="externosCursos" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="cursosLoading" :campos="cursosCampos" :listado="cursosListado">
			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" v-b-modal.add-new-curso>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:instructor="{ item }">
				<template v-if="item.instructorExternoDTO">
					<div
						:id="`popover-ins-${item.cuexIdCursoExterno}-${item.instructorExternoDTO.inexIdInstructorExterno}`"
					>
						{{ item.instructorExternoDTO.inexNombre }}
						{{ item.instructorExternoDTO.inexApaterno }}
						{{ item.instructorExternoDTO.inexAmaterno }}
					</div>
					<b-popover
						:target="`popover-ins-${item.cuexIdCursoExterno}-${item.instructorExternoDTO.inexIdInstructorExterno}`"
						triggers="hover"
						placement="top"
					>
						<div>
							<span class="text-smaller text-muted mr-1">Instructor</span>
							{{ item.instructorExternoDTO.inexNombre }}
							{{ item.instructorExternoDTO.inexApaterno }}
							{{ item.instructorExternoDTO.inexAmaterno }}
						</div>
						<div>
							<span class="text-smaller text-muted mr-1">Teléfono</span>
							{{ item.instructorExternoDTO.inexTelefono }}
						</div>
						<div>
							<span class="text-smaller text-muted mr-1">Correo</span>
							{{ item.instructorExternoDTO.inexCorreo }}
						</div>
					</b-popover>
				</template>
				<div v-else class="text-secondary text-small">Sin instructor</div>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="abrirHorarios(item)"
						v-b-tooltip.hover.top.ds300
						title="Horarios"
						><b-icon icon="clock-history"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarCurso(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Agregar curso -->
		<b-modal
			id="add-new-curso"
			@shown="focusField('campoAddNombre')"
			@cancel="resetForm"
			@ok="agregarCurso"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Agregar Curso</h5></template>

			<form ref="cursosAddForm" @submit.stop.prevent="agregarCurso">
				<b-form-group label="Clave" label-for="campoAddClave">
					<b-form-input
						id="campoAddClave"
						ref="campoAddClave"
						v-model.trim="$v.cursosAddForm.clave.$model"
						:state="validateState('cursosAddForm', 'clave')"
						:disabled="cursosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosAddFormClaveMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Nombre" label-for="campoAddNombre">
					<b-form-input
						id="campoAddNombre"
						ref="campoAddNombre"
						v-model.trim="$v.cursosAddForm.nombre.$model"
						:state="validateState('cursosAddForm', 'nombre')"
						:disabled="cursosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosAddFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Alumnos inscritos" label-for="campoAddAlumnos">
					<b-form-input
						id="campoAddAlumnos"
						ref="campoAddAlumnos"
						v-model.number="$v.cursosAddForm.alumnos.$model"
						:state="validateState('cursosAddForm', 'alumnos')"
						:disabled="cursosLoading"
						type="number"
						maxlength="4"
						min="0"
						max="9999"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosAddFormAlumnosMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="cursosLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="cursosLoading" @click="ok()">
					<b-spinner v-if="cursosLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar curso -->
		<b-modal
			id="update-curso"
			@shown="focusField('campoUpdateNombre')"
			@cancel="resetForm"
			@ok="actualizarCurso"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Editar Curso</h5></template>

			<form ref="cursosUpdateForm" @submit.stop.prevent="actualizarCurso">
				<b-form-group label="Clave" label-for="campoUpdateClave">
					<b-form-input
						id="campoUpdateClave"
						ref="campoUpdateClave"
						v-model.trim="$v.cursosUpdateForm.clave.$model"
						:state="validateState('cursosUpdateForm', 'clave')"
						:disabled="cursosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosUpdateFormClaveMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Nombre" label-for="campoUpdateNombre">
					<b-form-input
						id="campoUpdateNombre"
						ref="campoUpdateNombre"
						v-model.trim="$v.cursosUpdateForm.nombre.$model"
						:state="validateState('cursosUpdateForm', 'nombre')"
						:disabled="cursosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosUpdateFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Alumnos inscritos" label-for="campoUpdateAlumnos">
					<b-form-input
						id="campoUpdateAlumnos"
						ref="campoUpdateAlumnos"
						v-model.number="$v.cursosUpdateForm.alumnos.$model"
						:state="validateState('cursosUpdateForm', 'alumnos')"
						:disabled="cursosLoading"
						type="number"
						maxlength="4"
						min="0"
						max="9999"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ cursosUpdateFormAlumnosMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="cursosLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="cursosLoading" @click="ok()">
					<b-spinner v-if="cursosLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>