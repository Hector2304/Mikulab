<template>
	<div>
		<h2 class="text-secondary mb-0">{{ $options.pages[id].text }}</h2>
		<b-breadcrumb :items="breadcrumbs" class="rounded-0 pt-0 pl-1" style="background-color: unset"></b-breadcrumb>
	</div>
</template>

<script>
export default {
	props: {
		id: {
			type: String,
			default: ''
		},

		endcrumbs: {
			type: Array,
			default: () => []
		}
	},

	pages: {
		inicio: {
			text: 'Inicio',
			to: '/'
		},
		catalogos: {
			text: 'Catálogos',
			to: '/catalogos'
		},
		catalogoEquiposComputo: {
			text: 'Equipos de cómputo',
			to: '/catalogos/equipos-computo',
			breadcrumbs: ['catalogos']
		},
		catalogoLaboratorios: {
			text: 'Laboratorios',
			to: '/catalogos/laboratorios',
			breadcrumbs: ['catalogos']
		},
		catalogoLaboratoriosHorariosBloq: {
			text: 'Horarios bloqueados',
			to: '/catalogos/laboratorios/horarios-bloq',
			breadcrumbs: ['catalogos', 'catalogoLaboratorios']
		},
		catalogoLaboratoriosHorariosBloquear: {
			text: 'Bloquear',
			to: '/catalogos/laboratorios/horarios-bloq/bloquear',
			breadcrumbs: ['catalogos', 'catalogoLaboratorios', 'catalogoLaboratoriosHorariosBloq']
		},
		catalogoSoftware: {
			text: 'Software',
			to: '/catalogos/lab-software',
			breadcrumbs: ['catalogos']
		},
		profesor: {
			text: 'Profesor',
			to: '/profesor'
		},
		profesorReserva: {
			text: 'Reserva',
			to: '/profesor/reserva',
			breadcrumbs: ['profesor']
		},
		profesorReservaciones: {
			text: 'Reservaciones',
			to: '/profesor/reservaciones',
			breadcrumbs: ['profesor']
		},
		superusuario: {
			text: 'Superusuario',
			to: '/superusuario'
		},
		usuarios: {
			text: 'Usuarios',
			to: '/usuarios'
		},
		externos: {
			text: 'Externos',
			to: '/externos'
		},
		externosCursos: {
			text: 'Cursos',
			to: '/externos/cursos',
			breadcrumbs: ['externos']
		},
		externosCursosHorarios: {
			text: 'Horarios',
			to: '/externos/cursos/horarios',
			breadcrumbs: ['externos', 'externosCursos']
		},
		externosCursosHorariosSemana: {
			text: 'Horarios',
			to: '/externos/cursos/horarios/semana',
			breadcrumbs: ['externos', 'externosCursos', 'externosCursosHorarios']
		},
		externosInstructores: {
			text: 'Instructores',
			to: '/externos/instructores',
			breadcrumbs: ['externos']
		},
		servidorSocial: {
			text: 'Servidor social',
			to: '/servidor-social'
		},
		tecnico: {
			text: 'Técnico',
			to: '/tecnico'
		},
		servidorSocial: {
			text: 'Servidor Social',
			to: '/servidor-social'
		},
		bloqHorarios: {
			text: 'Horarios bloqueados',
			to: '/bloq-horarios'
		},
		bloqHorariosBloquear: {
			text: 'Bloquear horarios',
			to: '/bloq-horarios/bloquear',
			breadcrumbs: ['bloqHorarios']
		},
		bloqHorariosEditar: {
			text: 'Editar',
			to: '/bloq-horarios/editar',
			breadcrumbs: ['bloqHorarios']
		},
		reportes: {
			text: 'Reportes',
			to: '/reportes'
		},
		reportesBitacora: {
			text: 'Bitácora',
			to: '/reportes/bitacora',
			breadcrumbs: ['reportes']
		},
		reportesBitacoraEdicion: {
			text: 'Edición',
			to: '/reportes/bitacora/edicion',
			breadcrumbs: ['reportes', 'reportesBitacora']
		},
		reportesProgramadosDia: {
			text: 'Programados al día',
			to: '/reportes/programados-dia',
			breadcrumbs: ['reportes']
		},
		reportesProgramadosDiaEdicion: {
			text: 'Edición',
			to: '/reportes/programados-dia/edicion',
			breadcrumbs: ['reportes', 'reportesProgramadosDia']
		},
		reportesProgramadosSemana: {
			text: 'Programados semanal',
			to: '/reportes/programados-semana',
			breadcrumbs: ['reportes']
		}
	},

	computed: {
		breadcrumbs() {
			let bc = [];
			let push = (crumb) => {
				bc.push({
					...crumb,
					exact: true
				});
			};

			// Inicio
			push(this.$options.pages.inicio);

			if (this.$options.pages[this.id].breadcrumbs) {
				this.$options.pages[this.id].breadcrumbs
					.map((idBc) => this.$options.pages[idBc])
					.forEach((crumb) => push(crumb));
			}

			// Self
			if (this.$options.pages.inicio != this.$options.pages[this.id]) {
				push(this.$options.pages[this.id]);
			}

			// Endcrumbs
			this.endcrumbs.forEach((crumb) =>
				push({
					text: crumb,
					disabled: true
				})
			);

			return bc;
		}
	}
};
</script>