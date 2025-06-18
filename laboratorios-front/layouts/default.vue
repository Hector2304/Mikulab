<template>
  <div>
    <b-navbar toggleable="md" type="dark" variant="primary" sticky>
      <b-navbar-brand class="text-small mr-4" to="/">Laboratorios FCA</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav v-if="isSessionActive">
          <!-- ... los nav-items por tipo de usuario ... -->
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto" v-if="isSessionActive && personName && username">
          <!-- 🔔 ÍCONO DE MENSAJES CON BADGE Y CÍRCULO ROJO -->
          <b-nav-item
            to="/profesor/mensajes"
            v-if="userType === 'PROFESOR'"
            class="position-relative"
            @click="quitarNotificacionVisual"
          >
            <b-icon icon="envelope" variant="light" font-scale="1.2" />

            <!-- 🔴 Círculo rojo persistente -->
            <span
              v-if="mostrarNotificacionVisual"
              class="position-absolute bg-danger rounded-circle"
              style="
    width: 10px;
    height: 10px;
    top: 0;
    right: 0;
    z-index: 10;
    border: 2px solid white;
  "
            ></span>


            <!-- 🧮 Badge de número de mensajes no leídos -->
            <b-badge
              v-if="totalNoLeidos > 0"
              variant="danger"
              class="position-absolute top-0 start-100 translate-middle rounded-pill"
            >
              {{ totalNoLeidos }}
            </b-badge>
          </b-nav-item>

          <!-- Menú de sesión -->
          <b-nav-item-dropdown right>
            <template #button-content>{{ personName }} ({{ username }})</template>
            <b-dropdown-item @click="logout">Cerrar Sesión</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <main>
      <b-container style="min-height: calc(100vh - 56px)" class="d-flex flex-column p-3" fluid>
        <Nuxt />
      </b-container>
    </main>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
  middleware: 'sessionMiddleware',

  data() {
    return {
      totalNoLeidos: 0,
      mostrarNotificacionVisual: false // 🔴 Aquí se controla el círculo rojo
    };
  },

  computed: {
    ...mapState('sessionStore', ['isSessionActive', 'personName', 'username', 'userType'])
  },

  mounted() {
    this.getSession();
    this.obtenerTotalNoLeidos();

    // 🔄 Escuchar eventos
    this.$root.$on('actualizar-badge', this.obtenerTotalNoLeidos);
    this.$root.$on('mensaje-nuevo', this.activarNotificacionVisual);

    // 🔁 Restaurar si había notificación guardada
    this.mostrarNotificacionVisual = !!localStorage.getItem('mensajeNuevo');
  },

  beforeDestroy() {
    // 🧼 Limpieza de listeners para evitar fugas
    this.$root.$off('actualizar-badge', this.obtenerTotalNoLeidos);
    this.$root.$off('mensaje-nuevo', this.activarNotificacionVisual);
  },

  methods: {
    ...mapActions('sessionStore', ['getSession', 'logout']),

    async obtenerTotalNoLeidos() {
      try {
        const res = await this.$axios.get('/controller/mensajes/contar_no_leidos.php');
        this.totalNoLeidos = res.data.total;
      } catch (err) {
        console.error('Error al contar mensajes no leídos', err);
      }
    },

    limpiarBadge() {
      this.totalNoLeidos = 0;
    },

    // 🔴 Mostrar círculo rojo
    activarNotificacionVisual() {
      this.mostrarNotificacionVisual = true;
      localStorage.setItem('mensajeNuevo', '1');
    },

    // 🧼 Quitar círculo rojo (al dar clic)
    quitarNotificacionVisual() {
      this.mostrarNotificacionVisual = false;
      localStorage.removeItem('mensajeNuevo');
    }
  }
};
</script>
