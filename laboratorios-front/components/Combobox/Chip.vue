<template>
	<span :class="clazz">
		{{ item[itemLabelKey] }}
	</span>
</template>
<script>
export default {
	props: {
		item: {
			type: Object,
			required: true
		},
		itemLabelKey: {
			type: String,
			required: true
		}
	},

	computed: {
		clazz() {
			let theClazz = ['border', 'rounded', 'text-smaller', 'm-1', 'px-1 py-0', 'text-nowrap', 'text-noselect'];

			if (this.item.selected) {
				theClazz.push('text-primary', 'border-primary');
			} else {
				theClazz.push('text-muted');
			}

			return theClazz;
		}
	}
};
</script>
