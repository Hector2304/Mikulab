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
    this.cargarMensajes();
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
    formatFecha(fecha) {
      return new Date(fecha).toLocaleString('es-MX');
    }
  }
};
</script>
