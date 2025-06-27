<template>
  <div>
    <h3 class="mb-4">📬 Mensajes</h3>

    <b-card v-for="mensaje in mensajes" :key="mensaje.id_mensaje" class="mb-3">
      <b-card-header class="d-flex justify-content-between">
        <span><strong>{{ mensaje.titulo }}</strong></span>
        <small class="text-muted">{{ formatFecha(mensaje.fecha_envio) }}</small>
      </b-card-header>
      <b-card-body>
        <p>{{ mensaje.contenido }}</p>
        <b-badge :variant="mensaje.leido ? 'success' : 'warning'">
          {{ mensaje.leido ? 'Leído' : 'Sin leer' }}
        </b-badge>
      </b-card-body>
    </b-card>

    <b-alert v-if="mensajes.length === 0" show variant="info">
      No tienes mensajes por ahora.
    </b-alert>
  </div>
</template>

<script>
export default {
  data() {
    return {
      mensajes: []
    };
  },
  mounted() {
    this.cargarMensajes().then(() => {
      this.marcarTodosComoLeidos();
    });
  },
  methods: {
    async cargarMensajes() {
      try {
        const res = await this.$axios.get('/controller/mensajes/obtener.php');
        this.mensajes = res.data;
      } catch (err) {
        console.error('Error al obtener mensajes', err);
      }
    },

    async marcarTodosComoLeidos() {
      try {
        const idsNoLeidos = this.mensajes
          .filter(m => !m.leido)
          .map(m => m.id_mensaje);

        for (const id of idsNoLeidos) {
          await this.$axios.post('/controller/mensajes/marcar_leido.php', {
            id_mensaje: id
          });

          // 🧠 Marcar visualmente como leído localmente
          const msg = this.mensajes.find(m => m.id_mensaje === id);
          if (msg) {
            msg.leido = true;
          }
        }

        // 🔴 Eliminar circulito del sobre
        this.$root.$emit('mensaje-leido');

      } catch (err) {
        console.error('❌ Error al marcar todos como leídos', err);
      }
    },


    formatFecha(fecha) {
      return new Date(fecha).toLocaleString('es-MX');
    }
  }
};
</script>
