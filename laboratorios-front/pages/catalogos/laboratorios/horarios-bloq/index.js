import { mapState } from 'vuex';

export default {
	data(vm) {
		return {
			bloqLoading: false,
			bloqCampos: [
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
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			bloqListado: []
		};
	},

	computed: {
		...mapState('labHorariosStore', {
			horariosSelectedLab: 'selectedLab'
		})
	},

	mounted() {
		this.loadListado();
	},

	methods: {
		loadListado() {
			if (this.bloqLoading) {
				return;
			}

			this.bloqLoading = true;

			this.$axios
				.get(`controller/laboratorios/bloq-horario-listado.php?lab-id=${this.horariosSelectedLab.saloIdSalon}`)
				.then((response) => {
					for (let w of response.data) {
						let L = this.$moment().day(1).year(w.year).week(w.week);
						let D = this.$moment().day(7).year(w.year).week(w.week);

						this.bloqListado.push({
							...w,
							semana: `${L.format('DD MMM yy')} - ${D.format('DD MMM yy')}`,
							month: L.format('MMMM'),
							sortBy: `${L.format('yyMMDD')}-${D.format('yyMMDD')}`,
							monday: L.format('yyyy-MM-DD')
						});
					}
				})
				.catch((error) => {})
				.then(() => {
					this.bloqLoading = false;
				});
		},

		editarSemana(item) {
			this.$router.push({
				name: 'catalogos-laboratorios-horarios-bloq-bloquear',
				params: {
					...item
				}
			});
		},

		eliminarSemana(item) {
			this.$refs.confirmationPrompt.ask({
				title: 'Eliminar horarios bloqueados',
				text: `¿Realmente desea eliminar los horarios bloqueados de la semana <b>${item.semana}</b>?`,
				onConfirm: () => {
					return this.$axios
						.delete('controller/laboratorios/bloq-horario-delete-week.php', {
							data: {
								year: item.year,
								week: item.week,
								labId: this.horariosSelectedLab.saloIdSalon
							}
						})
						.then((response) => {
							this.bloqListado.splice(this.bloqListado.indexOf(item), 1);
							this.$toastSuccess({
								text: 'Horarios bloqueados eliminados.'
							});
						})
						.catch((error) => {})
						.then(() => {});
				}
			});
		}
	}
};
