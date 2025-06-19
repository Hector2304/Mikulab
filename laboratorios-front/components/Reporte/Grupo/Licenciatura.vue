<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Grupo</span>
			<span>{{ info.gr.grupClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Periodo</span>
			<span>{{ info.gr.grupIdPeriodo }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Semestre</span>
			<span>{{ info.gr.grupSemestre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Asignatura</span>
			<span>{{ info.gr.asigIdAsignatura }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Asignatura</span>
			<span>{{ info.gr.asigNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Carrera</span>
			<span>{{ info.gr.carrNombre }}</span>
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
