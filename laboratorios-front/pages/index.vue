<template>
	<b-row align-content="center" align-h="center" align-v="center" class="flex-grow-1">
		<b-col cols="12" sm="10" md="8" lg="6" xl="5">
			<b-card>
				<template #header>
					<div class="h5 m-0"><b-icon icon="shield-lock"></b-icon> Acceso</div>
				</template>

				<b-form ref="accesoForm" @submit.stop.prevent="login">
					<b-input-group>
						<b-input-group-prepend>
							<b-input-group-text>
								<b-icon icon="person-fill"></b-icon>
							</b-input-group-text>
						</b-input-group-prepend>
						<b-form-input
							type="text"
							id="accesoUsuario"
							ref="accesoUsuario"
							placeholder="Usuario"
							v-model.trim="accesoForm.usuario"
							maxlength="255"
							:disabled="requesting"
							required
						></b-form-input>
					</b-input-group>

					<b-input-group class="mt-2 mb-3">
						<b-input-group-prepend>
							<b-input-group-text>
								<b-icon icon="lock-fill"></b-icon>
							</b-input-group-text>
						</b-input-group-prepend>
						<b-form-input
							type="password"
							id="accesoContrasena"
							ref="accesoContrasena"
							placeholder="Contraseña"
							v-model="accesoForm.contrasena"
							maxlength="255"
							:disabled="requesting"
							required
						></b-form-input>
					</b-input-group>

					<div class="d-flex justify-content-end">
						<b-button type="submit" size="sm" variant="primary" :disabled="requesting">
							<b-spinner v-if="requesting" small></b-spinner>
							<span v-else>Iniciar sesión</span>
						</b-button>
					</div>
				</b-form>
			</b-card>
		</b-col>
	</b-row>
</template>

<script src="./index.js"></script>
