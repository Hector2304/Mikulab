<template>
	<div
		v-if="payload.isSmall"
		:style="{
			height: '100%',
			background: payload.info.bgColor,
			border: payload.info.brColor ? `2px dashed ${payload.info.brColor}` : undefined
		}"
	>
		<ReporteGrupoSmExterno v-if="payload.info.type === 'E'" :info="payload.info" />
		<ReporteGrupoSmLicenciatura v-else-if="payload.info.type === 'L'" :info="payload.info" />
		<ReporteGrupoSmPosgrado v-else-if="payload.info.type === 'P'" :info="payload.info" />
		<ReporteGrupoSmTitulacion v-else-if="payload.info.type === 'T'" :info="payload.info" />
	</div>
	<div v-else>
		<ReporteGrupoExterno v-if="payload.info.type === 'E'" :info="payload.info" />
		<ReporteGrupoLicenciatura v-else-if="payload.info.type === 'L'" :info="payload.info" />
		<ReporteGrupoPosgrado v-else-if="payload.info.type === 'P'" :info="payload.info" />
		<ReporteGrupoTitulacion v-else-if="payload.info.type === 'T'" :info="payload.info" />
	</div>
</template>

<script>
export default {
	props: {
		payload: {
			type: Object,
			default: () => ({})
		}
	}
};
</script>