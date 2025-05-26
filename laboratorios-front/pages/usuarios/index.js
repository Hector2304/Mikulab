  import formsMixin from '@/mixins/formsMixin';
  import { required, maxLength } from 'vuelidate/lib/validators';

  export default {
    mixins: [formsMixin],

    validations: {
      usuariosAddForm: {
        usuario: { required, maxLength: maxLength(255) },
        contrasena: { required, maxLength: maxLength(255) },
        nombre: { required, maxLength: maxLength(255) },
        aPaterno: { required, maxLength: maxLength(255) },
        aMaterno: { required, maxLength: maxLength(255) },
        tipoUsuario: { required }
      },
      usuariosUpdateForm: {
        usuario: { required, maxLength: maxLength(255) },
        nombre: { required, maxLength: maxLength(255) },
        aPaterno: { required, maxLength: maxLength(255) },
        aMaterno: { required, maxLength: maxLength(255) },
        tipoUsuario: { required }
      },
      usuariosPasswordForm: {
        contrasena: { required, maxLength: maxLength(255) }
      }
    },

    data() {
      return {
        usuariosCampos: [
          {
            key: 'usuaUsuario',
            label: 'Usuario',
            sortable: true,
            thClass: 'text-center align-middle',
            tdClass: 'align-middle'
          },
          {
            key: 'usuaNombre',
            label: 'Nombre',
            sortable: true,
            thClass: 'text-center align-middle',
            tdClass: 'align-middle'
          },
          {
            key: 'usuaApaterno',
            label: 'Apellido Paterno',
            sortable: true,
            thClass: 'text-center align-middle',
            tdClass: 'align-middle'
          },
          {
            key: 'usuaAmaterno',
            label: 'Apellido Materno',
            sortable: true,
            thClass: 'text-center align-middle',
            tdClass: 'align-middle'
          },
          {
            key: 'tiusNombre',
            label: 'Tipo usuario',
            sortable: true,
            thClass: 'text-center align-middle',
            tdClass: 'text-center align-middle'
          },
          {
            key: 'usuaStatus',
            label: 'Estatus',
            sortable: true,
            slotable: true,
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
        usuariosListado: [],
        usuariosLoading: false,
        usuariosAddForm: {
          usuario: null,
          contrasena: null,
          nombre: null,
          aPaterno: null,
          aMaterno: null,
          tipoUsuario: null
        },
        usuariosUpdateForm: {
          usuario: null,
          contrasena: null,
          nombre: null,
          aPaterno: null,
          aMaterno: null,
          tipoUsuario: null
        },
        usuariosPasswordForm: {
          contrasena: null
        },
        usuariosEditing: null
      };
    },

    computed: {
      usuariosAddFormUsuarioMsgs() {
        if (!this.$v.usuariosAddForm.usuario.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosAddForm.usuario.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosAddFormContrasenaMsgs() {
        if (!this.$v.usuariosAddForm.contrasena.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosAddForm.contrasena.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosAddFormNombreMsgs() {
        if (!this.$v.usuariosAddForm.nombre.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosAddForm.nombre.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosAddFormAPaternoMsgs() {
        if (!this.$v.usuariosAddForm.aPaterno.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosAddForm.aPaterno.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosAddFormAMaternoMsgs() {
        if (!this.$v.usuariosAddForm.aMaterno.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosAddForm.aMaterno.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosAddFormTipoUsuarioMsgs() {
        if (!this.$v.usuariosAddForm.tipoUsuario.required) {
          return 'Requerido.';
        }
        return '';
      },
      usuariosUpdateFormUsuarioMsgs() {
        if (!this.$v.usuariosUpdateForm.usuario.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosUpdateForm.usuario.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosUpdateFormNombreMsgs() {
        if (!this.$v.usuariosUpdateForm.nombre.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosUpdateForm.nombre.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosUpdateFormAPaternoMsgs() {
        if (!this.$v.usuariosUpdateForm.aPaterno.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosUpdateForm.aPaterno.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosUpdateFormAMaternoMsgs() {
        if (!this.$v.usuariosUpdateForm.aMaterno.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosUpdateForm.aMaterno.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      },
      usuariosUpdateFormTipoUsuarioMsgs() {
        if (!this.$v.usuariosUpdateForm.tipoUsuario.required) {
          return 'Requerido.';
        }
        return '';
      },
      usuariosPasswordFormContrasenaMsgs() {
        if (!this.$v.usuariosPasswordForm.contrasena.required) {
          return 'Requerido.';
        }
        if (!this.$v.usuariosPasswordForm.contrasena.maxLength) {
          return 'Máximo 255 caracteres.';
        }
        return '';
      }
    },

    mounted() {
      this.loadUsuarios();
    },

    methods: {
      loadUsuarios() {
        if (this.usuariosLoading) {
          return;
        }

        this.usuariosLoading = true;

        this.$axios
          .get('controller/usuario/listado.php')
          .then((response) => {
            this.usuariosListado = response.data;
          })
          .catch((error) => {})
          .then(() => {
            this.usuariosLoading = false;
          });
      },

      resetForm() {
        this.$v.$reset();
        this.usuariosAddForm.usuario = null;
        this.usuariosAddForm.contrasena = null;
        this.usuariosAddForm.nombre = null;
        this.usuariosAddForm.aPaterno = null;
        this.usuariosAddForm.aMaterno = null;
        this.usuariosAddForm.tipoUsuario = null;
        this.usuariosUpdateForm.usuario = null;
        this.usuariosUpdateForm.nombre = null;
        this.usuariosUpdateForm.aPaterno = null;
        this.usuariosUpdateForm.aMaterno = null;
        this.usuariosUpdateForm.tipoUsuario = null;
        this.usuariosPasswordForm.contrasena = null;
        this.usuariosEditing = null;
      },

      agregarUsuario(bvModalEvent) {
        if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

        if (this.usuariosLoading) return;

        this.$v.usuariosAddForm.$touch();
        if (this.$v.usuariosAddForm.$anyError) {
          return;
        }

        this.usuariosLoading = true;

        let payload = {
          usuario: this.usuariosAddForm.usuario,
          contrasena: this.usuariosAddForm.contrasena,
          nombre: this.usuariosAddForm.nombre,
          aPaterno: this.usuariosAddForm.aPaterno,
          aMaterno: this.usuariosAddForm.aMaterno,
          tipoUsuario: this.usuariosAddForm.tipoUsuario
        };

        this.$axios
          .post('controller/usuario/alta.php', payload)
          .then((response) => {
            this.usuariosListado.unshift(response.data);
            this.$bvModal.hide('add-new-usuario');
            this.resetForm();

            this.$toastSuccess({
              text: 'Usuario agregado.'
            });
          })
          .catch((error) => {})
          .then(() => {
            this.usuariosLoading = false;
          });
      },

      editarUsuario(item) {
        this.usuariosEditing = item;
        this.usuariosUpdateForm.usuario = item.usuaUsuario;
        this.usuariosUpdateForm.nombre = item.usuaNombre;
        this.usuariosUpdateForm.aPaterno = item.usuaApaterno;
        this.usuariosUpdateForm.aMaterno = item.usuaAmaterno;
        this.usuariosUpdateForm.tipoUsuario = item.usuaIdTipoUsuario;
        this.$bvModal.show('update-usuario');
      },

      actualizarUsuario(bvModalEvent) {
        if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

        if (this.usuariosLoading) return;

        this.$v.usuariosUpdateForm.$touch();
        if (this.$v.usuariosUpdateForm.$anyError) {
          return;
        }

        this.usuariosLoading = true;

        let payload = {
          id: this.usuariosEditing.usuaIdUsuario,
          nombre: this.usuariosUpdateForm.nombre,
          aPaterno: this.usuariosUpdateForm.aPaterno,
          aMaterno: this.usuariosUpdateForm.aMaterno,
          tipoUsuario: this.usuariosUpdateForm.tipoUsuario
        };

        this.$axios
          .patch('controller/usuario/modificacion.php', payload)
          .then((response) => {
            this.$set(this.usuariosEditing, 'usuaNombre', payload.nombre);
            this.$set(this.usuariosEditing, 'usuaApaterno', payload.aPaterno);
            this.$set(this.usuariosEditing, 'usuaAmaterno', payload.aMaterno);
            this.$set(this.usuariosEditing, 'usuaIdTipoUsuario', payload.tipoUsuario);
            this.$bvModal.hide('update-usuario');
            this.resetForm();
            this.$toastSuccess({
              text: 'Usuario editado.'
            });
          })
          .catch((error) => {})
          .then(() => {
            this.usuariosLoading = false;
          });
      },

      editarContrasena(item) {
        this.usuariosEditing = item;
        this.$bvModal.show('update-contrasena');
      },

      asignarContrasena(bvModalEvent) {
        if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

        if (this.usuariosLoading) return;

        this.$v.usuariosPasswordForm.$touch();
        if (this.$v.usuariosPasswordForm.$anyError) {
          return;
        }

        this.usuariosLoading = true;

        let payload = {
          id: this.usuariosEditing.usuaIdUsuario,
          contrasena: this.usuariosPasswordForm.contrasena
        };

        this.$axios
          .patch('controller/usuario/contrasena.php', payload)
          .then((response) => {
            this.$bvModal.hide('update-contrasena');
            this.resetForm();
            this.$toastSuccess({
              text: 'Usuario editado.'
            });
          })
          .catch((error) => {})
          .then(() => {
            this.usuariosLoading = false;
          });
      },

      toggleUsuarioStatus(item) {
        let activar = item.usuaStatus !== 'A';

        this.$refs.confirmationPrompt.ask({
          title: `${activar ? 'Activar' : 'Desactivar'} Usuario`,
          text: `¿Realmente desea ${
            activar ? 'activar' : 'desactivar'
          } al usuario <b><span class="text-primary">${item.usuaUsuario}</span> (${item.usuaNombre} ${
            item.usuaApaterno
          } ${item.usuaAmaterno})</b>?`,
          onConfirm: () => {
            return this.$axios
              .patch('controller/usuario/status.php', {
                id: item.usuaIdUsuario,
                status: activar ? 'A' : 'I'
              })
              .then((response) => {
                this.$set(item, 'usuaStatus', activar ? 'A' : 'I');
                this.$toastSuccess({
                  text: `Usuario ${activar ? 'activado' : 'desactivado'}`
                });
              })
              .catch((error) => {})
              .then(() => {});
          }
        });
      }
    }
  };
