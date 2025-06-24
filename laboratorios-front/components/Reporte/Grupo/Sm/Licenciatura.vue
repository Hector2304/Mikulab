<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Clave Grupo</span>
			<span class="text-small">{{ info.gr.grupClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Periodo</span>
			<span class="text-small">{{ info.gr.grupIdPeriodo }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Semestre</span>
			<span class="text-small">{{ info.gr.grupSemestre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Clave Asignatura</span>
			<span class="text-small">{{ info.gr.asigIdAsignatura }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Asignatura</span>
			<span class="text-small">{{ info.gr.asigNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Carrera</span>
			<span class="text-small">{{ info.gr.carrNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Profesor(a)</span>
			<span class="text-small">{{ info.pr.nombre }}</span>
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
