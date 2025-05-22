<template>
  <div>
    <Breadcrumbs id="superusuario" />

    <b-row align-v="stretch" align-h="around">
      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="person"
          text="Usuarios"
          @click.native="$router.push('/usuarios')"
        />
      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="layout-text-sidebar-reverse"
          text="Catálogos"
          @click.native="$router.push('/catalogos')"
        />
      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="book"
          text="Cursos externos"
          @click.native="$router.push('/externos')"
        />
      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="lock"
            text="Bloquear Horario"
          @click.native="$router.push('/catalogos/laboratorios')"
        />
      </b-col>

      <b-col cols="12" md="6" lg="5" xl="4" class="my-1">
        <SimpleMenuButton
          icon="file-earmark-spreadsheet"
          text="Reportes"
          @click.native="$router.push('/reportes')"
        />
      </b-col>
    </b-row>
  </div>
</template>
