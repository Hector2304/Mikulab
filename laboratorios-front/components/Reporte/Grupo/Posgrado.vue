<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Grupo</span>
			<span>{{ info.gr.gruoClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Periodo</span>
			<span>{{ info.gr.gruoIdPeriodo }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Asignatura</span>
			<span>{{ info.gr.asigIdAsignaturaP }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Asignatura</span>
			<span>{{ info.gr.asigNombreP }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Coordinación</span>
			<span>{{ info.gr.coorNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Profesor(a)</span>
			<span>{{ info.pr.nombre }}</span>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		info: {
			type: Object,
			default: () => ({})
		}
	}
};
</script>
