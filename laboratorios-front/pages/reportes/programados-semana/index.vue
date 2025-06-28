<template>
	<div>
		<Breadcrumbs id="reportesProgramadosSemana" />

		<b-card no-body>
			<div class="d-flex flex-row justify-content-center border-bottom">
				<b-form-datepicker
					placeholder="Seleccionar semana..."
					v-model="weekDate"
					v-bind="$options.CalendarLabels['es-MX']"
					:disabled="loading"
					:start-weekday="1"
					locale="es-MX"
					size="sm"
					@input="loadWeek"
					:date-info-fn="highlightWeek"
					button-variant="outline"
					button-only
					hide-header
				></b-form-datepicker>
				<div class="p-2">
					<span class="text-primary">Programación semanal</span>
					<span class="ml-1 ml-sm-4">{{ semana }}</span>
				</div>
				<b-button
					variant="outline-primary"
					class="border-0 ml-1 ml-sm-4"
					size="sm"
					@click="descargarExcel"
					v-b-tooltip.hover.top.ds300
					title="Descargar Excel"
				>
					<b-icon icon="file-earmark-spreadsheet"></b-icon>
				</b-button>
			</div>
			<div class="d-flex flex-row justify-content-center border-bottom">
				<div class="p-2 d-flex flex-column align-items-center justify-content-center">
					<b-dropdown
						size="sm"
						variant="secondary-outline"
						class="border-0 labs-dropdown"
						toggle-class=" d-flex flex-row flex-wrap align-items-center justify-content-center"
						:disabled="laboratoriosLoading"
					>
						<template #button-content>
							<b-icon icon="building" class="mr-2"></b-icon>

							<template v-if="laboratoriosLoading">
								<b-spinner variant="primary" small></b-spinner>
							</template>
							<template v-else>
								<template v-if="laboratorioSelected">
									<span class="text-primary">Laboratorio</span>
									<span class="ml-1 ml-sm-4">{{ laboratorioSelected.saloClave }}</span>
									<span class="ml-2 ml-sm-4 text-primary">Cupo</span>
									<span class="ml-1 ml-sm-4">{{ laboratorioSelected.saloCupo }}</span>
								</template>
								<template v-else>
									<span class="text-primary">Laboratorios</span>
								</template>
							</template>
						</template>

						<b-dropdown-group header="Laboratorios">
							<b-dropdown-item-button
								v-for="(item, i) of laboratorios"
								:key="`mcop-${i}`"
								class="py-1"
								:class="laboratorioSelected === item ? 'bg-light' : undefined"
								@click="selectLab(item)"
							>
								<div class="text-primary m-0">{{ item.saloClave }}</div>
								<div class="text-muted text-small">{{ item.saloUbicacion }}</div>
								<div class="text-muted text-smaller">
									Cupo: <span class="font-weight-bolder text-small">{{ item.saloCupo }}</span>
								</div>
							</b-dropdown-item-button>
						</b-dropdown-group>
					</b-dropdown>
				</div>
			</div>

			<div v-if="loading" class="d-flex p-4 justify-content-center align-items-center">
				<b-spinner variant="primary"></b-spinner>
			</div>
			<!-- v-show="!loading && weekDate" -->
			<SchedulerWeek ref="schedulerWeek" :sch-options="schOptions" />
		</b-card>
	</div>
</template>

<script src="./index.js"></script>

<style scoped>
.labs-dropdown /deep/ .dropdown-menu {
	max-height: 500px;
	overflow-y: auto;
}
</style>
