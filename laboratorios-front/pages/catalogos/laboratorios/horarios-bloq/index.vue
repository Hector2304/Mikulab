<template>
	<div>
		<Breadcrumbs id="catalogoLaboratoriosHorariosBloq" />

		<ConfirmationPrompt ref="confirmationPrompt" />

		<CatalogosListado :busy="bloqLoading" :campos="bloqCampos" :listado="bloqListado">
			<template v-slot:top-title>
				<div v-if="horariosSelectedLab" class="mr-sm-3 h6 mb-0 text-center">
					<span class="text-primary">Laboratorio</span>
					<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloClave }}</span>
					<span class="ml-1 ml-sm-4">{{ horariosSelectedLab.saloUbicacion }}</span>
				</div>
			</template>

			<template v-slot:top-buttons>
				<b-button
					variant="outline-primary"
					size="sm"
					class="ml-3"
					@click="$router.push('/catalogos/laboratorios/horarios-bloq/bloquear')"
				>
					<b-icon icon="plus"></b-icon> Bloquear
				</b-button>
			</template>

			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="editarSemana(item)"
						v-b-tooltip.hover.top.ds300
						title="Editar"
						><b-icon icon="pencil"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="eliminarSemana(item)"
						v-b-tooltip.hover.top.ds300
						title="Eliminar"
						><b-icon icon="trash"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>
	</div>
</template>

<script src="./index.js"></script>