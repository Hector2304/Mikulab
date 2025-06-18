import formsMixin from '@/mixins/formsMixin';
import { required, maxLength, email } from 'vuelidate/lib/validators';

export default {
	mixins: [formsMixin],

	validations: {
		instructoresAddForm: {
			nombre: { required, maxLength: maxLength(255) },
			aPaterno: { required, maxLength: maxLength(255) },
			aMaterno: { required, maxLength: maxLength(255) },
			telefono: { required, maxLength: maxLength(255) },
			correo: { required, maxLength: maxLength(255), email }
		},
		instructoresUpdateForm: {
			nombre: { required, maxLength: maxLength(255) },
			aPaterno: { required, maxLength: maxLength(255) },
			aMaterno: { required, maxLength: maxLength(255) },
			telefono: { required, maxLength: maxLength(255) },
			correo: { required, maxLength: maxLength(255), email }
		}
	},

	data() {
		return {
			// protected $inexIdInstructorExterno; // int
			// protected $inexNombre; // string
			// protected $inexApaterno; // string
			// protected $inexAmaterno; // string
			// protected $inexTelefono; // string
			// protected $inexCorreo; // string
			instructoresCampos: [
				{
					key: 'inexNombre',
					label: 'Nombre',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'inexApaterno',
					label: 'Apellido Paterno',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'inexAmaterno',
					label: 'Apellido Materno',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'inexTelefono',
					label: 'Teléfono',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'inexCorreo',
					label: 'Correo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			instructoresListado: [],
			instructoresLoading: false,
			instructoresAddForm: {
				nombre: null,
				aPaterno: null,
				aMaterno: null,
				telefono: null,
				correo: null
			},
			instructoresUpdateForm: {
				nombre: null,
				aPaterno: null,
				aMaterno: null,
				telefono: null,
				correo: null
			},
			instructoresEditing: null,

			cursosInstructorListado: [],
			cursosInstructorLoading: false,
			cursosEditing: false,
			cursosEditingLoading: false,
			cursosEditingListado: [],

			cursosCampos: [
				{
					key: 'cuexNombre',
					label: 'Curso',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				}
			]
		};
	},

	computed: {
		instructoresAddFormNombreMsgs() {
			if (!this.$v.instructoresAddForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresAddForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresAddFormAPaternoMsgs() {
			if (!this.$v.instructoresAddForm.aPaterno.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresAddForm.aPaterno.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresAddFormAMaternoMsgs() {
			if (!this.$v.instructoresAddForm.aMaterno.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresAddForm.aMaterno.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresAddFormTelefonoMsgs() {
			if (!this.$v.instructoresAddForm.telefono.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresAddForm.telefono.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresAddFormCorreoMsgs() {
			if (!this.$v.instructoresAddForm.correo.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresAddForm.correo.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			if (!this.$v.instructoresAddForm.correo.email) {
				return 'Formato de correo no válido.';
			}
			return '';
		},
		instructoresUpdateFormNombreMsgs() {
			if (!this.$v.instructoresUpdateForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresUpdateForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresUpdateFormAPaternoMsgs() {
			if (!this.$v.instructoresUpdateForm.aPaterno.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresUpdateForm.aPaterno.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresUpdateFormAMaternoMsgs() {
			if (!this.$v.instructoresUpdateForm.aMaterno.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresUpdateForm.aMaterno.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresUpdateFormTelefonoMsgs() {
			if (!this.$v.instructoresUpdateForm.telefono.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresUpdateForm.telefono.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		instructoresUpdateFormCorreoMsgs() {
			if (!this.$v.instructoresUpdateForm.correo.required) {
				return 'Requerido.';
			}
			if (!this.$v.instructoresUpdateForm.correo.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			if (!this.$v.instructoresAddForm.correo.email) {
				return 'Formato de correo no válido.';
			}
			return '';
		},

		cursosEditingFullList() {
			return this.cursosInstructorListado.concat(this.cursosEditingListado);
		}
	},

	mounted() {
		this.loadInstructores();
	},

	methods: {
		loadInstructores() {
			if (this.instructoresLoading) {
				return;
			}

			this.instructoresLoading = true;

			this.$axios
				.get('controller/externo/instructor/listado.php')
				.then((response) => {
					this.instructoresListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.instructoresLoading = false;
				});
		},

		resetForm() {
			this.$v.$reset();
			this.instructoresAddForm.nombre = null;
			this.instructoresAddForm.aPaterno = null;
			this.instructoresAddForm.aMaterno = null;
			this.instructoresAddForm.telefono = null;
			this.instructoresAddForm.correo = null;
			this.instructoresUpdateForm.nombre = null;
			this.instructoresUpdateForm.aPaterno = null;
			this.instructoresUpdateForm.aMaterno = null;
			this.instructoresUpdateForm.telefono = null;
			this.instructoresUpdateForm.correo = null;
			this.instructoresEditing = null;
		},

		agregarInstructor(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.instructoresLoading) return;

			this.$v.instructoresAddForm.$touch();
			if (this.$v.instructoresAddForm.$anyError) {
				return;
			}

			this.instructoresLoading = true;

			let payload = {
				nombre: this.instructoresAddForm.nombre,
				aPaterno: this.instructoresAddForm.aPaterno,
				aMaterno: this.instructoresAddForm.aMaterno,
				telefono: this.instructoresAddForm.telefono,
				correo: this.instructoresAddForm.correo
			};

			this.$axios
				.post('controller/externo/instructor/alta.php', payload)
				.then((response) => {
					this.instructoresListado.unshift(response.data);
					this.$bvModal.hide('add-new-instructor');
					this.resetForm();

					this.$toastSuccess({
						text: 'Instructor agregado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.instructoresLoading = false;
				});
		},

		editarInstructor(item) {
			this.instructoresEditing = item;
			this.instructoresUpdateForm.nombre = item.inexNombre;
			this.instructoresUpdateForm.aPaterno = item.inexApaterno;
			this.instructoresUpdateForm.aMaterno = item.inexAmaterno;
			this.instructoresUpdateForm.telefono = item.inexTelefono;
			this.instructoresUpdateForm.correo = item.inexCorreo;
			this.$bvModal.show('update-instructor');
		},

		actualizarInstructor(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.instructoresLoading) return;

			this.$v.instructoresUpdateForm.$touch();
			if (this.$v.instructoresUpdateForm.$anyError) {
				return;
			}

			this.instructoresLoading = true;

			let payload = {
				id: this.instructoresEditing.inexIdInstructorExterno,
				nombre: this.instructoresUpdateForm.nombre,
				aPaterno: this.instructoresUpdateForm.aPaterno,
				aMaterno: this.instructoresUpdateForm.aMaterno,
				telefono: this.instructoresUpdateForm.telefono,
				correo: this.instructoresUpdateForm.correo
			};

			this.$axios
				.patch('controller/externo/instructor/modificacion.php', payload)
				.then((response) => {
					this.$set(this.instructoresEditing, 'inexNombre', payload.nombre);
					this.$set(this.instructoresEditing, 'inexApaterno', payload.aPaterno);
					this.$set(this.instructoresEditing, 'inexAmaterno', payload.aMaterno);
					this.$set(this.instructoresEditing, 'inexTelefono', payload.telefono);
					this.$set(this.instructoresEditing, 'inexCorreo', payload.correo);
					this.$bvModal.hide('update-instructor');
					this.resetForm();
					this.$toastSuccess({
						text: 'Instructor editado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.instructoresLoading = false;
				});
		},

		abrirAsignarCursos(item) {
			if (this.cursosInstructorLoading) {
				return;
			}

			this.cursosInstructorLoading = true;

			this.instructoresEditing = item;
			this.cursosInstructorListado = [];
			this.$bvModal.show('asign-cursos');

			this.$axios
				.get(`controller/externo/curso/de-instructor.php?instructor-id=${item.inexIdInstructorExterno}`)
				.then((response) => {
					this.cursosInstructorListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.cursosInstructorLoading = false;
				});
		},

		startEditingCursos() {
			this.cursosEditing = true;

			if (this.cursosEditingLoading) {
				return;
			}

			this.cursosEditingLoading = true;

			this.cursosEditingListado = [];

			this.$axios
				.get('controller/externo/curso/sin-instructor.php')
				.then((response) => {
					for (let curso of this.cursosInstructorListado) {
						this.$set(curso, 'selected', true);
					}

					for (let curso of response.data) {
						this.$set(curso, 'selected', false);
						this.cursosEditingListado.push(curso);
					}
				})
				.catch((error) => {})
				.then(() => {
					this.cursosEditingLoading = false;
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
				this.$refs?.catalogosListadoCursos?.clearSelected();
				this.$refs?.catalogosListadocursosEditing?.clearSelected();
			});
		},

		guardarAsignacionCursos(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			let sinCambios = [];
			let deseleccionados = [];
			let seleccionados = [];

			for (let curso of this.cursosInstructorListado) {
				if (curso.selected) {
					sinCambios.push(curso);
				} else {
					deseleccionados.push(curso);
				}
			}

			for (let curso of this.cursosEditingListado) {
				if (curso.selected) {
					seleccionados.push(curso);
				}
			}

			if (deseleccionados.length <= 0 && seleccionados.length <= 0) {
				return;
			}

			let payload = {
				idInstructor: this.instructoresEditing.inexIdInstructorExterno,
				seleccionar: seleccionados.map(curso => curso.cuexIdCursoExterno),
				deseleccionar: deseleccionados.map(curso => curso.cuexIdCursoExterno)
			};

			this.cursosEditingLoading = true;

			this.$axios
				.patch('controller/externo/curso/asignacion-instructor.php', payload)
				.then((response) => {

					this.$toastSuccess({
						text: 'Cambios guardados.'
					});

					this.$nextTick(() => {
						this.cursosInstructorListado = [];
						for (let curso of sinCambios) {
							this.cursosInstructorListado.push(curso);
						}
						for (let curso of seleccionados) {
							this.cursosInstructorListado.push(curso);
						}
	
						this.cursosEditing = false;
					});
				})
				.catch((error) => {})
				.then(() => {
					this.cursosEditingLoading = false;
				});
		},

		cancelEditingCursos() {
			this.cursosEditing = false;
		}
	}
};
