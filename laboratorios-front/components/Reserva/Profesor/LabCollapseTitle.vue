<template>
  <div class="d-flex flex-column align-items-center">
		<span class="text-secondary h6">
			{{ lab.saloClave }} &nbsp;&nbsp;
			{{ $moment(date, 'yyyy-MM-DD').format('DD MMM yy') }}
		</span>
    <span
      class="text-primary font-weight-bold mt-1"
      style="font-size: 1.25rem; background-color: rgba(0,123,255,0.1); padding: 6px 14px; border-radius: 8px;"
    >
	🕒 Horario reservado: {{ desde }}:00 - {{ hasta }}:00
</span>

  </div>
</template>


<script>
export default {
	props: {
		lab: {
			type: Object,
			default: () => ({})
		},
		date: {
			type: String,
			default: ''
		},
		hour: {
			type: Object,
			default: () => ({})
		}
	},

	computed: {
		desde () {
			return this.hour.hora;
		},
		hasta () {
			let current = this.hour;

			while(current = current.next) {
				if (!current.selected) {
					break;
				}
			}

			return current?.hora;
		}
	}
};
</script>
