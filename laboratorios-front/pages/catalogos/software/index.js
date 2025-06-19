import formsMixin from '@/mixins/formsMixin';
import { required, maxLength } from 'vuelidate/lib/validators';

export default {
	mixins: [formsMixin],

	validations: {
		softwareAddForm: {
			software: { required, maxLength: maxLength(255) }
		},
		softwareUpdateForm: {
			software: { required, maxLength: maxLength(255) }
		}
	},

	data() {
		return {
			softwareCampos: [
				{
					key: 'softNombre',
					label: 'Nombre',
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
			softwareListado: [],
			softwareAddForm: {
				software: null
			},
			softwareUpdateForm: {
				software: null
			},
			softwareLoading: false,
			sofwareEditing: null
		};
	},

	computed: {
		softwareAddFormMsgs() {
			if (!this.$v.softwareAddForm.software.required) {
				return 'Requerido.';
			}
			if (!this.$v.softwareAddForm.software.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		softwareUpdateFormMsgs() {
			if (!this.$v.softwareUpdateForm.software.required) {
				return 'Requerido.';
			}
			if (!this.$v.softwareUpdateForm.software.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		}
	},

	mounted() {
		this.loadSoftwares();
	},

	methods: {
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

		resetFormSoftware() {
			this.$v.$reset();
			this.softwareAddForm.software = null;
			this.softwareUpdateForm.software = null;
			this.sofwareEditing = null;
		},

		agregarSoftware(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.softwareLoading) return;

			this.$v.softwareAddForm.$touch();
			if (this.$v.softwareAddForm.$anyError) {
				return;
			}

			this.softwareLoading = true;

			let payload = {
				softNombre: this.softwareAddForm.software
			};

			this.$axios
				.post('controller/software/alta.php', payload)
				.then((response) => {
					this.softwareListado.unshift(response.data);
					this.$bvModal.hide('add-new-software');
					this.resetFormSoftware();

					this.$toastSuccess({
						text: 'Software agregado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.softwareLoading = false;
				});
		},

		editarSofware(item) {
			this.sofwareEditing = item;
			this.softwareUpdateForm.software = item.softNombre;
			this.$bvModal.show('update-software');
		},

		actualizarSoftware(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.softwareLoading) return;

			this.$v.softwareUpdateForm.$touch();
			if (this.$v.softwareUpdateForm.$anyError) {
				return;
			}

			this.softwareLoading = true;

			let payload = {
				softNombre: this.softwareUpdateForm.software,
				softIdSoftware: this.sofwareEditing.softIdSoftware
			};

			this.$axios
				.patch('controller/software/modificacion.php', payload)
				.then((response) => {
					this.$set(this.softwareListado, this.softwareListado.indexOf(this.sofwareEditing), payload);
					this.$bvModal.hide('update-software');
					this.resetFormSoftware();
					this.$toastSuccess({
						text: 'Software editado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.softwareLoading = false;
				});
		},

		eliminarSoftware(item) {
			this.$refs.confirmationPrompt.ask({
				title: 'Eliminar software',
				text: `¿Realmente desea eliminar <b>${item.softNombre}</b>?`,
				onConfirm: () => {
					return this.$axios
						.delete('controller/software/baja.php', {
							data: {
								softIdSoftware: item.softIdSoftware
							}
						})
						.then((response) => {
							this.softwareListado.splice(this.softwareListado.indexOf(item), 1);
							this.$toastSuccess({
								text: 'Software eliminado.'
							});
						})
						.catch((error) => {
							if (error?.response?.status === 400) {
								if (error?.response?.data?.error === 'INTEGRITY') {
									this.$toastPrimary({
										title: 'Imposible eliminar',
										text: 'Software utilizado por algún laboratorio.'
									});
								}
							}
						})
						.then(() => {});
				}
			});
		}
	}
};
