<template>
	<div>
		<Breadcrumbs id="usuarios" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="usuariosLoading" :campos="usuariosCampos" :listado="usuariosListado">
			<template v-slot:top-buttons>
				<b-button variant="outline-primary" size="sm" class="ml-3" v-b-modal.add-new-usuario>
					<b-icon icon="plus"></b-icon> Agregar
				</b-button>
			</template>

			<template v-slot:usuaStatus="{ item }">
				<div v-if="item.usuaStatus === 'A'">
					<b-icon icon="check" variant="success"></b-icon>
					<span class="text-success">Activo</span>
				</div>
				<div v-else-if="item.usuaStatus === 'I'">
					<b-icon icon="x" variant="danger"></b-icon>
					<span class="text-danger">Inactivo</span>
				</div>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarUsuario(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarContrasena(item)"
						v-b-tooltip.hover.top.ds300
						title="Asignar contraseña"
						><b-icon icon="input-cursor-text"></b-icon
					></b-button>
					<b-button
						v-if="item.usuaIdUsuario !== 1"
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="toggleUsuarioStatus(item)"
						v-b-tooltip.hover.top.ds300
						:title="item.usuaStatus === 'A' ? 'Desactivar usuario' : 'Activar usuario'"
						><b-icon :icon="item.usuaStatus === 'A' ? 'person-x' : 'person-check'"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Agregar usuario -->
		<b-modal
			id="add-new-usuario"
			@shown="focusField('campoAddUsuario')"
			@cancel="resetForm"
			@ok="agregarUsuario"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Agregar Usuario</h5></template>

			<form ref="usuariosAddForm" @submit.stop.prevent="agregarUsuario">
				<b-form-group label="Usuario" label-for="campoAddUsuario">
					<b-form-input
						id="campoAddUsuario"
						ref="campoAddUsuario"
						v-model.trim="$v.usuariosAddForm.usuario.$model"
						:state="validateState('usuariosAddForm', 'usuario')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosAddFormUsuarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Contraseña" label-for="campoAddContrasena">
					<b-form-input
						type="password"
						id="campoAddContrasena"
						ref="campoAddContrasena"
						v-model="$v.usuariosAddForm.contrasena.$model"
						:state="validateState('usuariosAddForm', 'contrasena')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosAddFormContrasenaMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Nombre" label-for="campoAddNombre">
					<b-form-input
						id="campoAddNombre"
						ref="campoAddNombre"
						v-model.trim="$v.usuariosAddForm.nombre.$model"
						:state="validateState('usuariosAddForm', 'nombre')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosAddFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Paterno" label-for="campoAddAPaterno">
					<b-form-input
						id="campoAddAPaterno"
						ref="campoAddAPaterno"
						v-model.trim="$v.usuariosAddForm.aPaterno.$model"
						:state="validateState('usuariosAddForm', 'aPaterno')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosAddFormAPaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Materno" label-for="campoAddAMaterno">
					<b-form-input
						id="campoAddAMaterno"
						ref="campoAddAMaterno"
						v-model.trim="$v.usuariosAddForm.aMaterno.$model"
						:state="validateState('usuariosAddForm', 'aMaterno')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosAddFormAMaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Tipo de usuario" label-for="campoAddTipoUsuario">
					<b-form-select
						id="campoAddTipoUsuario"
						ref="campoAddTipoUsuario"
						v-model.trim="$v.usuariosAddForm.tipoUsuario.$model"
						:state="validateState('usuariosAddForm', 'tipoUsuario')"
						:disabled="usuariosLoading"
					>
						<b-form-select-option :value="2">TECNICO</b-form-select-option>
						<b-form-select-option :value="3">SERVIDOR SOCIAL</b-form-select-option>
						<b-form-select-option :value="4">VIGILANTE</b-form-select-option>
					</b-form-select>

					<b-form-invalid-feedback>{{ usuariosAddFormTipoUsuarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="usuariosLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="usuariosLoading" @click="ok()">
					<b-spinner v-if="usuariosLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Editar usuario -->
		<b-modal
			id="update-usuario"
			@shown="focusField('campoUpdateNombre')"
			@cancel="resetForm"
			@ok="actualizarUsuario"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Editar Usuario</h5></template>

			<form ref="usuariosUpdateForm" @submit.stop.prevent="actualizarUsuario">
				<b-form-group label="Usuario" label-for="campoUpdateUsuario">
					<b-form-input
						id="campoUpdateUsuario"
						ref="campoUpdateUsuario"
						v-model.trim="$v.usuariosUpdateForm.usuario.$model"
						:state="validateState('usuariosUpdateForm', 'usuario')"
						disabled
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosUpdateFormUsuarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Nombre" label-for="campoUpdateNombre">
					<b-form-input
						id="campoUpdateNombre"
						ref="campoUpdateNombre"
						v-model.trim="$v.usuariosUpdateForm.nombre.$model"
						:state="validateState('usuariosUpdateForm', 'nombre')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosUpdateFormNombreMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Paterno" label-for="campoUpdateAPaterno">
					<b-form-input
						id="campoUpdateAPaterno"
						ref="campoUpdateAPaterno"
						v-model.trim="$v.usuariosUpdateForm.aPaterno.$model"
						:state="validateState('usuariosUpdateForm', 'aPaterno')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosUpdateFormAPaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Apellido Materno" label-for="campoUpdateAMaterno">
					<b-form-input
						id="campoUpdateAMaterno"
						ref="campoUpdateAMaterno"
						v-model.trim="$v.usuariosUpdateForm.aMaterno.$model"
						:state="validateState('usuariosUpdateForm', 'aMaterno')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosUpdateFormAMaternoMsgs }}</b-form-invalid-feedback>
				</b-form-group>
				<b-form-group label="Tipo de usuario" label-for="campoUpdateTipoUsuario">
					<b-form-select
						v-model.trim="$v.usuariosUpdateForm.tipoUsuario.$model"
						:state="validateState('usuariosUpdateForm', 'tipoUsuario')"
						:disabled="$v.usuariosUpdateForm.tipoUsuario.$model === 1 || usuariosLoading"
					>
						<b-form-select-option v-if="$v.usuariosUpdateForm.tipoUsuario.$model === 1" :value="1"
							>SUPERUSUARIO</b-form-select-option
						>
						<b-form-select-option :value="2">TECNICO</b-form-select-option>
						<b-form-select-option :value="3">SERVIDOR_SOCIAL</b-form-select-option>
						<b-form-select-option :value="4">VIGILANTE</b-form-select-option>
					</b-form-select>

					<b-form-invalid-feedback>{{ usuariosUpdateFormTipoUsuarioMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="usuariosLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="usuariosLoading" @click="ok()">
					<b-spinner v-if="usuariosLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>

		<!-- Agregar usuario -->
		<b-modal
			id="update-contrasena"
			@shown="focusField('campoPasswordContrasena')"
			@cancel="resetForm"
			@ok="asignarContrasena"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Asignar contraseña</h5></template>

			<div class="mb-2" v-if="usuariosEditing">

				<span class="text-primary">Usuario</span>
				<span class="ml-1 ml-sm-4">
					{{ usuariosEditing.usuaNombre }}
					{{ usuariosEditing.usuaApaterno }}
					{{ usuariosEditing.usuaAmaterno }}
				</span>
				<span class="ml-1 ml-sm-4">
					({{ usuariosEditing.usuaUsuario }})
				</span>
			</div>

			<form ref="usuariosPasswordForm" @submit.stop.prevent="asignarContrasena">
				<b-form-group label="Contraseña" label-for="campoPasswordContrasena">
					<b-form-input
						type="password"
						id="campoPasswordContrasena"
						ref="campoPasswordContrasena"
						v-model="$v.usuariosPasswordForm.contrasena.$model"
						:state="validateState('usuariosPasswordForm', 'contrasena')"
						:disabled="usuariosLoading"
						maxlength="255"
						required
					></b-form-input>

					<b-form-invalid-feedback>{{ usuariosPasswordFormContrasenaMsgs }}</b-form-invalid-feedback>
				</b-form-group>
			</form>

			<template #modal-footer="{ ok, cancel }">
				<b-button size="sm" variant="secondary" :disabled="usuariosLoading" @click="cancel()">
					Cancelar
				</b-button>
				<b-button size="sm" variant="primary" :disabled="usuariosLoading" @click="ok()">
					<b-spinner v-if="usuariosLoading" small></b-spinner>
					<span v-else>Confirmar</span>
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>
