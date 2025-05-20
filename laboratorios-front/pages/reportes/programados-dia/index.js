export default {
	data(vm) {
		return {
			loading: false,
			campos: [
				{
					key: 'reprFecha',
					label: 'Fecha',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true,
					formatter(value, key, item) {
						return vm.$moment(item.reprFecha, 'yyyy-MM-DD').format('DD MMMM yy');
					},
					filterByFormatted: true
				},
				{
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			listado: [],

			today: vm.$moment.tz('America/Mexico_City').format('yyyy-MM-DD')
		};
	},

	mounted() {
		this.loadListado();
	},

	methods: {
		loadListado() {
			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$axios
				.get('controller/reportes/programado-dia/listado.php')
				.then((response) => {
					this.listado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		openCrearReporte() {
			this.$bvModal.show('crear-reporte-modal');
		},

		crearReporte(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$axios
				.post('controller/reportes/programado-dia/alta.php', {
					fecha: this.today
				})
				.then((response) => {
					this.goToEdicion(response.data);
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		goToEdicion(item) {
			this.$router.push({
				name: 'reportes-programados-dia-edicion',
				params: {
					reporte: {
						...item
					}
				}
			});
		}
	}
};
