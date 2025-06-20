import { mapActions } from 'vuex';

export default {
	data() {
		return {
			labCampos: [
				{
					key: 'saloClave',
					label: 'Clave',
					sortable: true
				},
				{
					key: 'saloUbicacion',
					label: 'Ubicación',
					sortable: true
				},
				{
					key: 'saloCupo',
					label: 'Cupo',
					sortable: true,
					class: 'text-center align-middle'
				},
				{
					key: 'actions',
					label: 'Acciones',
					class: 'text-center align-middle',
					style: 'min-width: 120px;',
					slotable: true
				}
			],
			labListado: [],
			labLoading: false,
			labSelected: null,
			labSelectedSoftware: [],
			labSelectedSoftwareLoading: false,

			/* *** */

			softwareListado: [],
			softwareLoading: false,

			/* *** */

			equiposCampos: [
				{
					key: 'eqcoNombre',
					label: 'Nombre',
					sortable: true
				},
				{
					key: 'eqcoDescripcion',
					label: 'Descripción',
					sortable: true
				},
				{
					key: 'eqcoNumeroInventario',
					label: 'Número de Inventario',
					sortable: true
				},
				{
					key: 'eqcoStatus',
					label: 'Estatus',
					sortable: true
				}
			],

			equiposLabListado: [],
			equiposLabLoading: false,
			equiposEditing: false,
			equiposEditingLoading: false,
			equiposEditingListado: []
		};
	},

	computed: {
		equiposEditingFullList() {
			return this.equiposLabListado.concat(this.equiposEditingListado);
		}
	},

	mounted() {
		this.loadLaboratorios();
		this.loadSoftwares();
	},

	methods: {
		...mapActions('labHorariosStore', {
			selectLabAction: 'selectLab'
		}),

		loadLaboratorios() {
			if (this.labLoading) {
				return;
			}

			this.labLoading = true;

			this.$axios
				.get('controller/laboratorios/listado.php')
				.then((response) => {
					this.labListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.labLoading = false;
				});
		},

		loadSoftwares() {
			if (this.softwareLoading) {
				return;
			}

			this.softwareLoading = true;

			this.$axios
				.get('controller/software/listado.php')
				.then((response) => {
					this.softwareListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.softwareLoading = false;
				});
		},

		abrirAsignarEquipos(item) {
			if (this.equiposLabLoading) {
				return;
			}

			this.equiposLabLoading = true;

			this.labSelected = item;
			this.equiposLabListado = [];
			this.$bvModal.show('asign-equipos-computo');

			this.$axios
				.get(`controller/equipo-computo/de-laboratorio.php?lab-id=${item.saloIdSalon}`)
				.then((response) => {
					this.equiposLabListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.equiposLabLoading = false;
				});
		},

		startEditingEquipos() {
			this.equiposEditing = true;

			if (this.equiposEditingLoading) {
				return;
			}

			this.equiposEditingLoading = true;

			this.equiposEditingListado = [];

			this.$axios
				.get('controller/equipo-computo/sin-laboratorio.php')
				.then((response) => {
					for (let equipo of this.equiposLabListado) {
						this.$set(equipo, 'selected', true);
					}

					for (let equipo of response.data) {
						this.$set(equipo, 'selected', false);
						this.equiposEditingListado.push(equipo);
					}
				})
				.catch((error) => {})
				.then(() => {
					this.equiposEditingLoading = false;
				});
		},

		tbodyTrClass(item) {
			// https://github.com/bootstrap-vue/bootstrap-vue/issues/4459#issuecomment-562473511
			if (item?.selected) {
				return ['b-table-row-selected', 'table-primary', 'cursor-pointer', 'text-primary'];
			} else {
				return ['cursor-pointer'];
			}
		},

		rowClicked(item) {
			if (item.selected) {
				this.$set(item, 'selected', false);
			} else {
				this.$set(item, 'selected', true);
			}

			this.$nextTick(() => {
				this.$refs?.catalogosListadoEquipos?.clearSelected();
				this.$refs?.catalogosListadoEquiposEditing?.clearSelected();
			});
		},

		guardarAsignacionEquipos(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			let sinCambios = [];
			let deseleccionados = [];
			let seleccionados = [];

			for (let eqco of this.equiposLabListado) {
				if (eqco.selected) {
					sinCambios.push(eqco);
				} else {
					deseleccionados.push(eqco);
				}
			}

			for (let eqco of this.equiposEditingListado) {
				if (eqco.selected) {
					seleccionados.push(eqco);
				}
			}

			if (deseleccionados.length <= 0 && seleccionados.length <= 0) {
				return;
			}

			let payload = {
				saloIdSalon: this.labSelected.saloIdSalon,
				seleccionar: seleccionados.map((eqco) => eqco.eqcoIdEquipo),
				deseleccionar: deseleccionados.map((eqco) => eqco.eqcoIdEquipo)
			};

			this.equiposEditingLoading = true;

			this.$axios
				.patch('controller/equipo-computo/asignacion-laboratorio.php', payload)
				.then((response) => {
					this.$toastSuccess({
						text: 'Cambios guardados.'
					});

					this.$nextTick(() => {
						this.equiposLabListado = [];
						for (let eqco of sinCambios) {
							this.equiposLabListado.push(eqco);
						}
						for (let eqco of seleccionados) {
							this.equiposLabListado.push(eqco);
						}

						this.equiposEditing = false;
					});
				})
				.catch((error) => {})
				.then(() => {
					this.equiposEditingLoading = false;
				});
		},

		cancelEditingEquipos() {
			this.equiposEditing = false;
		},

		abrirAsignarSoftware(item) {
			if (this.labSelectedSoftwareLoading) {
				return;
			}

			this.labSelectedSoftwareLoading = true;

			this.labSelected = item;
			this.$bvModal.show('asign-software');

			this.$axios
				.get(`controller/software/laboratorio-get.php?lab-id=${item.saloIdSalon}`)
				.then((response) => {
					this.labSelectedSoftware = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.labSelectedSoftwareLoading = false;
				});
		},

		cerrarAsignacion() {
			this.labSelected = null;
			this.labSelectedSoftware = [];
		},

		checkInitValue(soft) {
			return this.labSelectedSoftware.some((lss) => lss.lasoIdSoftware === soft.softIdSoftware);
		},

		abrirHorarios(item) {
			this.selectLabAction(item);
			this.$router.push('/catalogos/laboratorios/horarios-bloq');
		}
	}
};
