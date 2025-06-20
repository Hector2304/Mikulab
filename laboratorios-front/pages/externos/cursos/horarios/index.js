import { mapState } from 'vuex';

export default {
	middleware: 'cursoHorarioMiddleware',

	data(vm) {
		return {
			horariosLoading: false,
			horariosCampos: [
				{
					key: 'semana',
					label: 'Semana',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true,
					sortByFormatted(value, key, item) {
						return item.sortBy;
					}
				},
				{
					key: 'year',
					label: 'Año',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true
				},
				{
					key: 'week',
					label: 'No. de semana',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true
				},
				{
					key: 'month',
					label: 'Mes',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true
				},
				{
					key: 'lab',
					label: 'Laboratorio',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true,
					formatter(value, key, item) {
						return item?.lab?.saloClave ?? '';
					},
					sortByFormatted: true,
				},
				{
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			horariosListado: []
		};
	},

	computed: {
		...mapState('cursoExternoStore', {
			horariosSelectedCurso: 'selectedCurso'
		})
	},

	mounted() {
		this.loadListado();
	},

	methods: {
		loadListado() {
			if (this.horariosLoading) {
				return;
			}

			this.horariosLoading = true;

			this.$axios
				.get(
					`controller/externo/curso/asignacion-horario-listado.php?curso-id=${this.horariosSelectedCurso.cuexIdCursoExterno}`
				)
				.then((response) => {
					for (let w of response.data) {
						let L = this.$moment().day(1).year(w.year).week(w.week);
						let D = this.$moment().day(7).year(w.year).week(w.week);

						this.horariosListado.push({
							...w,
							semana: `${L.format('DD MMM yy')} - ${D.format('DD MMM yy')}`,
							month: L.format('MMMM'),
							sortBy: `${L.format('yyMMDD')}-${D.format('yyMMDD')}`,
							monday: L.format('yyyy-MM-DD'),
							lab: w.lab
						});
					}
				})
				.catch((error) => {})
				.then(() => {
					this.horariosLoading = false;
				});
		},

		editarSemana(item) {
			this.$router.push({
				name: 'externos-cursos-horarios-semana',
				params: {
					...item
				}
			});
		},

		eliminarSemana(item) {
			this.$refs.confirmationPrompt.ask({
				title: 'Eliminar horarios',
				text: `¿Realmente desea eliminar los horarios de la semana <b>${item.semana}</b>?`,
				onConfirm: () => {
					return this.$axios
						.delete('controller/externo/curso/asignacion-horario-delete-week.php', {
							data: {
								year: item.year,
								week: item.week,
								labId: item.lab.saloIdSalon,
								cursoId: this.horariosSelectedCurso.cuexIdCursoExterno
							}
						})
						.then((response) => {
							this.horariosListado.splice(this.horariosListado.indexOf(item), 1);
							this.$toastSuccess({
								text: 'Horarios eliminados.'
							});
						})
						.catch((error) => {})
						.then(() => {});
				}
			});
		}
	}
};
