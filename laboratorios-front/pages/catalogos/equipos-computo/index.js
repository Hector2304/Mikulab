import formsMixin from '@/mixins/formsMixin';
import { required, maxLength } from 'vuelidate/lib/validators';

export default {
	mixins: [formsMixin],

	validations: {
		equiposAddForm: {
			nombre: { required, maxLength: maxLength(255) },
			descripcion: { required, maxLength: maxLength(1000) },
			numeroInventario: { required, maxLength: maxLength(255) },
			status: { required, maxLength: maxLength(255) }
		},
		equiposUpdateForm: {
			nombre: { required, maxLength: maxLength(255) },
			descripcion: { required, maxLength: maxLength(1000) },
			numeroInventario: { required, maxLength: maxLength(255) },
			status: { required, maxLength: maxLength(255) }
		}
	},

	data() {
		return {
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
				},
				{
					key: 'actions',
					label: 'Acciones',
					class: 'text-center align-middle',
					style: 'min-width: 120px;',
					slotable: true
				}
			],
			equiposListado: [],
			equiposAddForm: {
				nombre: null,
				descripcion: null,
				numeroInventario: null,
				status: null
			},
			equiposUpdateForm: {
				nombre: null,
				descripcion: null,
				numeroInventario: null,
				status: null
			},
			equiposLoading: false,
			equiposEditing: null,
			equiposLabAsignado: null,
			equiposLabAsignadoLoading: false
		};
	},

	computed: {
		equiposAddFormNombreMsgs() {
			if (!this.$v.equiposAddForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposAddForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		equiposAddFormDescripcionMsgs() {
			if (!this.$v.equiposAddForm.descripcion.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposAddForm.descripcion.maxLength) {
				return 'Máximo 1000 caracteres.';
			}
			return '';
		},
		equiposAddFormNumeroInventarioMsgs() {
			if (!this.$v.equiposAddForm.numeroInventario.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposAddForm.numeroInventario.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		equiposAddFormStatusMsgs() {
			if (!this.$v.equiposAddForm.status.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposAddForm.status.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		equiposUpdateFormNombreMsgs() {
			if (!this.$v.equiposUpdateForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposUpdateForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		equiposUpdateFormDescripcionMsgs() {
			if (!this.$v.equiposUpdateForm.descripcion.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposUpdateForm.descripcion.maxLength) {
				return 'Máximo 1000 caracteres.';
			}
			return '';
		},
		equiposUpdateFormNumeroInventarioMsgs() {
			if (!this.$v.equiposUpdateForm.numeroInventario.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposUpdateForm.numeroInventario.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		equiposUpdateFormStatusMsgs() {
			if (!this.$v.equiposUpdateForm.status.required) {
				return 'Requerido.';
			}
			if (!this.$v.equiposUpdateForm.status.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		}
	},

	mounted() {
		this.loadEquipos();
	},

	methods: {
		loadEquipos() {
			if (this.equiposLoading) {
				return;
			}

			this.equiposLoading = true;

			this.$axios
				.get('controller/equipo-computo/listado.php')
				.then((response) => {
					this.equiposListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.equiposLoading = false;
				});
		},

		resetForm() {
			this.$v.$reset();
			this.equiposAddForm.nombre = null;
			this.equiposAddForm.descripcion = null;
			this.equiposAddForm.numeroInventario = null;
			this.equiposAddForm.status = null;
			this.equiposUpdateForm.nombre = null;
			this.equiposUpdateForm.descripcion = null;
			this.equiposUpdateForm.numeroInventario = null;
			this.equiposUpdateForm.status = null;
			this.equiposEditing = null;
		},

		agregarEquipo(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.equiposLoading) return;

			this.$v.equiposAddForm.$touch();
			if (this.$v.equiposAddForm.$anyError) {
				return;
			}

			this.equiposLoading = true;

			let payload = {
				eqNombre: this.equiposAddForm.nombre,
				eqDescripcion: this.equiposAddForm.descripcion,
				eqNumeroInventario: this.equiposAddForm.numeroInventario,
				eqStatus: this.equiposAddForm.status
			};

			this.$axios
				.post('controller/equipo-computo/alta.php', payload)
				.then((response) => {
					this.equiposListado.unshift(response.data);
					this.$bvModal.hide('add-new-equipo');
					this.resetForm();

					this.$toastSuccess({
						text: 'Equipo de Cómputo agregado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.equiposLoading = false;
				});
		},

		editarEquipo(item) {
			this.equiposEditing = item;
			this.equiposUpdateForm.nombre = item.eqcoNombre;
			this.equiposUpdateForm.descripcion = item.eqcoDescripcion;
			this.equiposUpdateForm.numeroInventario = item.eqcoNumeroInventario;
			this.equiposUpdateForm.status = item.eqcoStatus;
			this.$bvModal.show('update-equipo');
		},

		actualizarEquipo(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.equiposLoading) return;

			this.$v.equiposUpdateForm.$touch();
			if (this.$v.equiposUpdateForm.$anyError) {
				return;
			}

			this.equiposLoading = true;

			let payload = {
				eqcoIdEquipo: this.equiposEditing.eqcoIdEquipo,
				eqcoNombre: this.equiposUpdateForm.nombre,
				eqcoDescripcion: this.equiposUpdateForm.descripcion,
				eqcoNumeroInventario: this.equiposUpdateForm.numeroInventario,
				eqcoStatus: this.equiposUpdateForm.status
			};

			this.$axios
				.patch('controller/equipo-computo/modificacion.php', payload)
				.then((response) => {
					this.$set(this.equiposListado, this.equiposListado.indexOf(this.equiposEditing), payload);
					this.$bvModal.hide('update-equipo');
					this.resetForm();
					this.$toastSuccess({
						text: 'Equipo de Cómputo editado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.equiposLoading = false;
				});
		},

		eliminarEquipo(item) {
			this.$refs.confirmationPrompt.ask({
				title: 'Eliminar equipo de cómputo',
				text: `¿Realmente desea eliminar el equipo <b>${item.eqcoNombre}</b>?`,
				onConfirm: () => {
					return this.$axios
						.delete('controller/equipo-computo/baja.php', {
							data: {
								eqcoIdEquipo: item.eqcoIdEquipo
							}
						})
						.then((response) => {
							this.equiposListado.splice(this.equiposListado.indexOf(item), 1);
							this.$toastSuccess({
								text: 'Equipo de Cómputo eliminado.'
							});
						})
						.catch((error) => {
							if (error?.response?.status === 400) {
								if (error?.response?.data?.error === 'INTEGRITY') {
									this.$toastPrimary({
										title: 'Imposible eliminar',
										text: 'Equipo de Cómputo utilizado por algún laboratorio o registrado en alguna asistencia.'
									});
								}
							}
						})
						.then(() => {});
				}
			});
		},

		getLaboratorioAsignado(item) {
			if (this.equiposLabAsignadoLoading) {
				return;
			}

			this.equiposLabAsignado = null;

			this.$bvModal.show('lab-asignado-modal');

			if (!item.eqcoIdSalon) {
				return;
			}

			this.equiposLabAsignadoLoading = true;

			this.$axios
				.get(`controller/equipo-computo/laboratorio-get.php?lab-id=${item.eqcoIdSalon}`)
				.then((response) => {
					this.equiposLabAsignado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.equiposLabAsignadoLoading = false;
				});
		}
	}
};
