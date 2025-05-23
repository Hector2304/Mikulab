<template>
  <div>
    <Breadcrumbs id="superusuario" />

    <b-row align-v="stretch" align-h="around">
      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="person"
          text="Usuarios"
          v-b-tooltip.hover.top
          title="Gestiona los usuarios registrados en el sistema"
          @click.native="$router.push('/usuarios')"
        />

      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="layout-text-sidebar-reverse"
          text="Catálogos"
          v-b-tooltip.hover.top
          title="Administra catálogos base como laboratorios, asignaturas, etc."
          @click.native="$router.push('/catalogos')"
        />

      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="book"
          text="Cursos externos"
          v-b-tooltip.hover.top
          title="Registra y consulta cursos impartidos fuera de la facultad"
          @click.native="$router.push('/externos')"
        />

      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="shield-lock"
          text="Bloquear Horario"
          v-b-tooltip.hover.top
          title="Establece horarios no disponibles en los laboratorios"
          @click.native="$router.push('/catalogos/laboratorios')"
        />

      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="file-earmark-spreadsheet"
          text="Reportes"
          v-b-tooltip.hover.top
          title="Consulta reportes sobre laboratorios, usuarios y horarios"
          @click.native="$router.push('/reportes')"
        />

      </b-col>
    </b-row>
  </div>
</template>
