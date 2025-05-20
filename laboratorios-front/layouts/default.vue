<template>
	<div>
		<b-navbar toggleable="md" type="dark" variant="primary" sticky>
			<b-navbar-brand class="text-small mr-4" to="/">Laboratorios FCA</b-navbar-brand>

			<b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

			<b-collapse id="nav-collapse" is-nav>
				<b-navbar-nav v-if="isSessionActive">
					<template v-if="userType === 'SUPERUSUARIO'">
						<b-nav-item to="/usuarios">Usuarios</b-nav-item>

						<b-nav-item-dropdown text="Catálogos" right>
							<b-dropdown-item to="/catalogos/equipos-computo">Equipos de cómputo</b-dropdown-item>
							<b-dropdown-item to="/catalogos/software">Software</b-dropdown-item>
							<b-dropdown-item to="/catalogos/laboratorios">Laboratorios</b-dropdown-item>
						</b-nav-item-dropdown>

						<b-nav-item-dropdown text="Externos" right>
							<b-dropdown-item to="/externos/cursos">Cursos</b-dropdown-item>
							<b-dropdown-item to="/externos/instructores">Instructores</b-dropdown-item>
						</b-nav-item-dropdown>

						<b-nav-item-dropdown text="Reportes" right>
							<b-dropdown-item to="/reportes/bitacora">Bitácora</b-dropdown-item>
							<b-dropdown-item to="/reportes/programados-dia">Programados al día</b-dropdown-item>
							<b-dropdown-item to="/reportes/programados-semana">Programados semanal</b-dropdown-item>
						</b-nav-item-dropdown>
					</template>

					<template v-if="userType === 'PROFESOR'">
						<b-nav-item-dropdown text="Reservaciones" right>
							<b-dropdown-item to="/profesor/reserva">Nueva</b-dropdown-item>
							<b-dropdown-item to="/profesor/reservaciones">Anteriores</b-dropdown-item>
						</b-nav-item-dropdown>
					</template>

					<template v-if="userType === 'TECNICO'">
						<b-nav-item-dropdown text="Reportes" right>
							<b-dropdown-item to="/reportes/bitacora">Bitácora</b-dropdown-item>
							<b-dropdown-item to="/reportes/programados-dia">Programados al día</b-dropdown-item>
							<b-dropdown-item to="/reportes/programados-semana">Programados semanal</b-dropdown-item>
						</b-nav-item-dropdown>
					</template>

					<template v-if="userType === 'SERVIDOR_SOCIAL'">
						<b-nav-item-dropdown text="Reportes" right>
							<b-dropdown-item to="/reportes/programados-dia">Programados al día</b-dropdown-item>
							<b-dropdown-item to="/reportes/programados-semana">Programados semanal</b-dropdown-item>
						</b-nav-item-dropdown>
					</template>
				</b-navbar-nav>

				<b-navbar-nav class="ml-auto" v-if="isSessionActive && personName && username">
					<b-nav-item-dropdown right>
						<template #button-content>{{ personName }} ({{ username }})</template>
						<b-dropdown-item @click="logout">Cerrar Sesión</b-dropdown-item>
					</b-nav-item-dropdown>
				</b-navbar-nav>
			</b-collapse>
		</b-navbar>

		<main>
			<b-container style="min-height: calc(100vh - 56px)" class="d-flex flex-column p-3" fluid>
				<Nuxt />
			</b-container>
		</main>
	</div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
	middleware: 'sessionMiddleware',

	computed: {
		...mapState('sessionStore', ['isSessionActive', 'personName', 'username', 'userType'])
	},

	mounted() {
		this.getSession();
	},

	methods: {
		...mapActions('sessionStore', ['getSession', 'logout'])
	}
};
</script>
