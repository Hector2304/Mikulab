<template>
	<b-card>
		<b-card-title>
			<div class="d-flex flex-row flex-wrap justify-content-center align-items-center">
				<div class="flex-grow-2 d-flex flex-row flex-wrap justify-content-center align-items-center">
					<slot name="top-title"></slot>
				</div>

				<div class="flex-grow-1 my-2" v-if="!hideSearch">
					<b-input-group size="sm">
						<b-form-input v-model.trim="filtro" size="sm" placeholder="Buscar" type="search"></b-form-input>
						<b-input-group-append is-text>
							<b-icon icon="search"></b-icon>
						</b-input-group-append>
					</b-input-group>
				</div>

				<div class="flex-grow-2 d-flex flex-row flex-wrap">
					<slot name="top-buttons"></slot>
				</div>
			</div>
		</b-card-title>

		<b-card-text>
			<b-table
				class="my-3"
				:id="tableId"
				:ref="`${tableId}`"
				responsive
				stacked="sm"
				:busy="busy"
				:items="listado"
				:fields="listado.length > 0 ? campos : undefined"
				:per-page="porPagina"
				:current-page="paginaActual"
				:filter="filtro"
				:selectable="selectable"
				:select-mode="selectMode"
				:tbody-tr-class="tbodyTrClass"
				:thead-class="theadClass"
				@row-clicked="(it, idx, e) => $emit('row-clicked', it, idx, e)"
				show-empty
				small
				:hover="hover"
			>
				<template #table-busy>
					<div class="mt-4 text-secondary text-small text-center">
						<b-spinner variant="primary"></b-spinner>
					</div>
				</template>
				<template #empty>
					<div class="mt-4 text-secondary text-small text-center">Sin datos</div>
				</template>
				<template #emptyfiltered>
					<div class="mt-4 text-secondary text-small text-center">Sin coincidencias</div>
				</template>

				<template v-for="slt of slotables" v-slot:[`cell(${slt.key})`]="data">
					<slot :name="slt.key" v-bind="data"></slot>
				</template>
			</b-table>

			<div class="w-100 d-flex flex-row justify-content-end" v-if="rows > 10">
				<div class="mr-3 d-flex flex-row justify-content-end align-items-center">
					<span class="text-small text-muted mr-1 text-nowrap">Por página</span>
					<b-form-select v-model="porPagina" :options="[10, 25, 50, 100]" size="sm"></b-form-select>
				</div>
				<div class="d-flex flex-row justify-content-center align-items-center">
					<b-pagination
						class="mb-0"
						v-model="paginaActual"
						:total-rows="rows"
						:per-page="porPagina"
						:aria-controls="tableId"
						align="fill"
						size="sm"
						first-number
						last-number
						pills
					></b-pagination>
				</div>
			</div>
		</b-card-text>
	</b-card>
</template>

<script>
export default {
	props: {
		tableId: {
			type: String,
			default: 'catalogos-table'
		},
		campos: {
			type: Array,
			default: () => []
		},
		listado: {
			type: Array,
			default: () => []
		},
		busy: {
			type: Boolean,
			default: false
		},
		tbodyTrClass: {
			type: Function,
			default: undefined
		},
		selectable: {
			type: Boolean,
			default: false
		},
		selectMode: {
			type: String,
			default: 'single'
		},
		theadClass: {
			type: String,
			default: undefined
		},
		hideSearch: {
			type: Boolean,
			default: false
		},
		hover: {
			type: Boolean,
			default: true
		}
	},

	data() {
		return {
			paginaActual: 1,
			porPagina: 10,
			filtro: null
		};
	},

	computed: {
		rows() {
			return this?.listado?.length ?? 0;
		},

		slotables() {
			return this.campos.filter((c) => c.slotable);
		}
	},

	methods: {
		clearSelected() {
			this.$refs[this.tableId].clearSelected();
		}
	}
};
</script>