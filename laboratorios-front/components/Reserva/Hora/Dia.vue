<template>
	<div class="d-flex flex-row flex-wrap justify-content-center align-content-stretch">
		<ReservaHoraChip
			v-for="(h, i) of horasShown"
			:key="`${i}-rhc`"
			:hora="h"
			:tamanho-seleccion="tamanhoSeleccion"
			@select="(h) => $emit('select', h)"
		/>
	</div>
</template>

<script>
export default {
	props: {
		tamanhoSeleccion: {
			type: Number,
			default: 1
		},
		disponibilidad: {
			type: Object,
			default: () => ({})
		},
		isSabado: {
			type: Boolean,
			default: false
		},
		labId: {
			type: String,
			required: true
		}
	},

	data() {
		let horas = [];

		let prev = undefined;
		for (let i = 7; i <= 22; i++) {
			let hora = {
				prev: prev
			};

			if (prev) {
				prev.next = hora;
			}

			if (i < 10) {
				hora.hora = `0${i}`;
			} else {
				hora.hora = `${i}`;
			}

			horas.push(hora);
			prev = hora;
		}

		return { horas };
	},

	computed: {
		horasShown() {
			for (let hr of this.horas) {
				this.$set(hr, 'disabled', false);

				if (this.isSabado) {
					if (Number(hr.hora) >= 13) {
						this.$set(hr, 'disabled', true);
					}
				} else {
					if (Number(hr.hora) >= 22) {
						this.$set(hr, 'disabled', true);
					}
				}

				if (this.disponibilidad[this.labId] && this.disponibilidad[this.labId].has(Number(hr.hora))) {
					this.$set(hr, 'disabled', true);
				}
			}

			if (this.isSabado) {
				return this.horas.slice(0, 7);
			}
			return this.horas;
		}
	}
};
</script>
