import formsMixin from '@/mixins/formsMixin';
import { mapActions } from 'vuex';
import { required, maxLength, numeric, integer, minValue, maxValue } from 'vuelidate/lib/validators';

export default {
	mixins: [formsMixin],

	validations: {
		cursosAddForm: {
			clave: { required, maxLength: maxLength(255) },
			nombre: { required, maxLength: maxLength(255) },
			alumnos: {
				required,
				numeric,
				integer,
				minValue: minValue(0),
				maxValue: maxValue(9999),
				maxLength: maxLength(4)
			}
		},
		cursosUpdateForm: {
			clave: { required, maxLength: maxLength(255) },
			nombre: { required, maxLength: maxLength(255) },
			alumnos: {
				required,
				numeric,
				integer,
				minValue: minValue(0),
				maxValue: maxValue(9999),
				maxLength: maxLength(4)
			}
		}
	},

	data() {
		return {
			// protected $cuexIdCursoExterno; // int
			// protected $cuexClave; // string
			// protected $cuexNombre; // string
			// protected $cuexAlumnosInscritos; // int
			// protected $cuexIdInstructorExterno; // int
			// protected $instructorExternoDTO; // InstructorExternoDTO
			cursosCampos: [
				{
					key: 'cuexClave',
					label: 'Clave',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'cuexNombre',
					label: 'Nombre',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
        /*
				{
					key: 'cuexAlumnosInscritos',
					label: 'Alumnos inscritos',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},*/
				{
					key: 'instructor',
					label: 'Instructor',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true,
					formatter(value, key, item) {
						let insDTO = item.instructorExternoDTO;
						if (!insDTO) {
							return '';
						}

						return insDTO.inexNombre + ' ' + insDTO.inexApaterno + ' ' + insDTO.inexAmaterno;
					},
					sortByFormatted: true,
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
			cursosListado: [],
			cursosLoading: false,
			cursosAddForm: {
				clave: null,
				nombre: null,
				alumnos: null
			},
			cursosUpdateForm: {
				clave: null,
				nombre: null,
				alumnos: null
			},
			cursosEditing: null
		};
	},

	computed: {
		cursosAddFormClaveMsgs() {
			if (!this.$v.cursosAddForm.clave.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosAddForm.clave.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		cursosAddFormNombreMsgs() {
			if (!this.$v.cursosAddForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosAddForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		cursosAddFormAlumnosMsgs() {
			if (!this.$v.cursosAddForm.alumnos.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosAddForm.alumnos.numeric) {
				return 'Sólo números.';
			}
			if (!this.$v.cursosAddForm.alumnos.integer) {
				return 'Sólo números enteros.';
			}
			if (!this.$v.cursosAddForm.alumnos.minValue) {
				return 'Mínimo cero.';
			}
			if (!this.$v.cursosAddForm.alumnos.maxValue) {
				return 'Máximo 9999.';
			}
			if (!this.$v.cursosAddForm.alumnos.maxLength) {
				return 'Máximo 4 cifras.';
			}
			return '';
		},
		cursosUpdateFormClaveMsgs() {
			if (!this.$v.cursosUpdateForm.clave.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosUpdateForm.clave.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		cursosUpdateFormNombreMsgs() {
			if (!this.$v.cursosUpdateForm.nombre.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosUpdateForm.nombre.maxLength) {
				return 'Máximo 255 caracteres.';
			}
			return '';
		},
		cursosUpdateFormAlumnosMsgs() {
			if (!this.$v.cursosUpdateForm.alumnos.required) {
				return 'Requerido.';
			}
			if (!this.$v.cursosUpdateForm.alumnos.numeric) {
				return 'Sólo números.';
			}
			if (!this.$v.cursosUpdateForm.alumnos.integer) {
				return 'Sólo números enteros.';
			}
			if (!this.$v.cursosUpdateForm.alumnos.minValue) {
				return 'Mínimo cero.';
			}
			if (!this.$v.cursosUpdateForm.alumnos.maxValue) {
				return 'Máximo 9999.';
			}
			if (!this.$v.cursosUpdateForm.alumnos.maxLength) {
				return 'Máximo 4 cifras.';
			}
			return '';
		},
	},

	mounted() {
		this.loadCursos();
	},

	methods: {
		...mapActions('cursoExternoStore', {
			selectCursoAction: 'selectCurso'
		}),

		loadCursos() {
			if (this.cursosLoading) {
				return;
			}

			this.cursosLoading = true;

			this.$axios
				.get('controller/externo/curso/listado.php')
				.then((response) => {
					this.cursosListado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.cursosLoading = false;
				});
		},

		resetForm() {
			this.$v.$reset();
			this.cursosAddForm.clave = null;
			this.cursosAddForm.nombre = null;
			this.cursosAddForm.alumnos = null;
			this.cursosUpdateForm.clave = null;
			this.cursosUpdateForm.nombre = null;
			this.cursosUpdateForm.alumnos = null;
			this.cursosEditing = null;
		},

		agregarCurso(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.cursosLoading) return;

			this.$v.cursosAddForm.$touch();
			if (this.$v.cursosAddForm.$anyError) {
				return;
			}

			this.cursosLoading = true;

			let payload = {
				clave: this.cursosAddForm.clave,
				nombre: this.cursosAddForm.nombre,
				alumnos: this.cursosAddForm.alumnos,
			};

			this.$axios
				.post('controller/externo/curso/alta.php', payload)
				.then((response) => {
					this.cursosListado.unshift(response.data);
					this.$bvModal.hide('add-new-curso');
					this.resetForm();

					this.$toastSuccess({
						text: 'Curso agregado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.cursosLoading = false;
				});
		},

		editarCurso(item) {
			this.cursosEditing = item;
			this.cursosUpdateForm.clave = item.cuexClave;
			this.cursosUpdateForm.nombre = item.cuexNombre;
			this.cursosUpdateForm.alumnos = item.cuexAlumnosInscritos;
			this.$bvModal.show('update-curso');
		},

		actualizarCurso(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.cursosLoading) return;

			this.$v.cursosUpdateForm.$touch();
			if (this.$v.cursosUpdateForm.$anyError) {
				return;
			}

			this.cursosLoading = true;

			let payload = {
				id: this.cursosEditing.cuexIdCursoExterno,
				clave: this.cursosUpdateForm.clave,
				nombre: this.cursosUpdateForm.nombre,
				alumnos: this.cursosUpdateForm.alumnos,
			};

			this.$axios
				.patch('controller/externo/curso/modificacion.php', payload)
				.then((response) => {
					this.$set(this.cursosEditing, 'cuexClave', payload.clave);
					this.$set(this.cursosEditing, 'cuexNombre', payload.nombre);
					this.$set(this.cursosEditing, 'cuexAlumnosInscritos', payload.alumnos);
					this.$bvModal.hide('update-curso');
					this.resetForm();
					this.$toastSuccess({
						text: 'Curso editado.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.cursosLoading = false;
				});
		},

		abrirHorarios(item) {
			this.selectCursoAction(item);
			this.$router.push('/externos/cursos/horarios');
		}
	}
};
