<template>
	<div>
		<Breadcrumbs id="catalogoLaboratorios" />

		<!-- Laboratorios -->
		<CatalogosListado :busy="labLoading" :campos="labCampos" :listado="labListado">
			<template v-slot:actions="{ item }">
				<div style="min-width: 120px">
          <b-button
            variant="outline-danger"
            class="border-0"
            size="sm"
            @click="abrirHorarios(item)"
            v-b-tooltip.hover.top.ds300
            title="Horarios bloqueados"
          >
            <b-icon icon="shield-lock"></b-icon>
          </b-button>

          <b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="abrirAsignarEquipos(item)"
						v-b-tooltip.hover.top.ds300
						title="Asignar Equipos de Cómputo"
						><b-icon icon="display"></b-icon
					></b-button>
					<b-button
						variant="outline-primary"
						class="border-0"
						size="sm"
						@click="abrirAsignarSoftware(item)"
						v-b-tooltip.hover.top.ds300
						title="Asignar Software"
						><b-icon icon="terminal"></b-icon
					></b-button>
				</div>
			</template>
		</CatalogosListado>

		<!-- Asignar equipos -->
		<b-modal
			id="asign-equipos-computo"
			@ok="guardarAsignacionEquipos"
			size="xl"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Equipos de Cómputo</h5></template>

			<div v-if="labSelected">
				<span class="text-primary">Laboratorio</span>
				<span class="ml-1 ml-sm-4">{{ labSelected.saloClave }}</span>
				<span class="ml-1 ml-sm-4">{{ labSelected.saloUbicacion }}</span>
			</div>

			<hr />

			<template v-if="!equiposEditing">
				<CatalogosListado
					id="catalogosListadoEquipos"
					key="catalogosListadoEquipos"
					ref="catalogosListadoEquipos"
					:busy="equiposLabLoading"
					:campos="equiposCampos"
					:listado="equiposLabListado"
				>
					<template v-slot:top-buttons>
						<b-button
							variant="outline-primary"
							size="sm"
							class="ml-3"
							:disabled="equiposLabLoading"
							@click="startEditingEquipos"
						>
							<b-icon icon="pencil"></b-icon> Editar
						</b-button>
					</template>
				</CatalogosListado>
			</template>

			<template v-else>
				<CatalogosListado
					id="catalogosListadoEquiposEditing"
					key="catalogosListadoEquiposEditing"
					ref="catalogosListadoEquiposEditing"
					:busy="equiposEditingLoading"
					:campos="[
						{
							key: 'selected',
							label: '',
							class: 'text-center align-middle',
							slotable: true
						},
						...equiposCampos
					]"
					:listado="equiposEditingFullList"
					:tbody-tr-class="tbodyTrClass"
					@row-clicked="rowClicked"
					selectable
					select-mode="multi"
				>
					<template v-slot:top-title>
						<span class="text-primary h6 m-0 mr-2">Seleccionar Equipos</span>
					</template>

					<template v-slot:selected="{ item }">
						<span class="mx-1" v-html="item.selected ? '&check;' : '&nbsp;'"></span>
					</template>
				</CatalogosListado>
			</template>

			<template #modal-footer="{ ok, cancel }">
				<template v-if="equiposEditing">
					<b-button
						size="sm"
						variant="secondary"
						:disabled="equiposLabLoading || equiposEditingLoading"
						@click="cancelEditingEquipos"
					>
						Cancelar
					</b-button>
					<b-button
						size="sm"
						variant="primary"
						:disabled="equiposLabLoading || equiposEditingLoading"
						@click="ok()"
					>
						<b-spinner v-if="equiposLabLoading || equiposEditingLoading" small></b-spinner>
						<span v-else>Confirmar</span>
					</b-button>
				</template>

				<template v-else>
					<b-button
						size="sm"
						variant="secondary"
						:disabled="equiposLabLoading || equiposEditingLoading"
						@click="cancel()"
					>
						Cerrar
					</b-button>
				</template>
			</template>
		</b-modal>

		<!-- Asignar software -->
		<b-modal
			id="asign-software"
			@cancel="cerrarAsignacion"
			@ok="cerrarAsignacion"
			no-close-on-esc
			no-close-on-backdrop
			hide-header-close
			scrollable
			centered
		>
			<template #modal-header><h5 class="text-primary">Asignar Software</h5></template>

			<div v-if="labSelected">
				<span class="text-primary">Laboratorio</span>
				<span class="ml-1 ml-sm-4">{{ labSelected.saloClave }}</span>
				<span class="ml-1 ml-sm-4">{{ labSelected.saloUbicacion }}</span>
			</div>

			<hr />

			<div class="text-primary mb-2">Software</div>

			<div class="d-flex justify-content-center align-items-center" v-if="labSelectedSoftwareLoading">
				<b-spinner variant="primary"></b-spinner>
			</div>
			<div
				class="d-flex flex-row flex-wrap justify-content-between"
				v-else-if="labSelected && !labSelectedSoftwareLoading"
			>
				<CatalogosSoftwareCheck
					v-for="(soft, i) in softwareListado"
					:key="'csc-' + i"
					:software="soft"
					:id-laboratorio="labSelected.saloIdSalon"
					:init-value="checkInitValue(soft)"
				/>
			</div>

			<template #modal-footer="{ cancel }">
				<b-button size="sm" variant="secondary" @click="cancel()" :disabled="labSelectedSoftwareLoading">
					Cerrar
				</b-button>
			</template>
		</b-modal>
	</div>
</template>

<script src="./index.js"></script>
