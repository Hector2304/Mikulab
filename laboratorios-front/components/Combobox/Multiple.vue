<template>
	<div class="d-flex flex-row flex-nowrap align-items-center justify-content-start">
		<b-dropdown
			size="sm"
			variant="secondary-outline"
			class="border rounded"
			style="border-color: #ced4da !important"
			toggle-class=" d-flex flex-row flex-wrap align-items-center justify-content-center"
		>
			<template #button-content>
				<template v-if="someSelected">
					<ComboboxChip
						v-for="(sw, i) of itemsSelected"
						:key="`cbmcbc-${i}`"
						:item="sw"
						:item-label-key="itemLabelKey"
					/>
				</template>
				<template v-else>
					<span class="text-muted">{{ label }}</span>
				</template>
			</template>

			<b-dropdown-group :header="label">
				<b-dropdown-item-button
					v-for="(item, i) of items"
					:key="`mcop-${i}`"
					:variant="item.selected ? 'primary' : undefined"
					@click.native.capture.stop="toggleSelectItem(item)"
				>
					<b-icon :icon="item.selected ? 'check' : 'blank'"></b-icon>
					{{ item[itemLabelKey] }}
				</b-dropdown-item-button>
			</b-dropdown-group>

			<b-dropdown-divider></b-dropdown-divider>

			<b-dropdown-item-button
				variant="secondary"
				@click.native.capture.stop="toggleSelectAll(true)"
			>
				<b-icon icon="check"></b-icon>
				Todos
			</b-dropdown-item-button>

			<b-dropdown-item-button
				variant="secondary"
				@click.native.capture.stop="toggleSelectAll(false)"
			>
				<b-icon icon="x"></b-icon>
				Ninguno
			</b-dropdown-item-button>
		</b-dropdown>
	</div>
</template>

<script>
export default {
	props: {
		items: {
			type: Array,
			required: true
		},
		label: {
			type: String,
			required: true
		},
		itemLabelKey: {
			type: String,
			required: true
		}
	},

	computed: {
		itemsSelected() {
			if (!this.items) {
				return [];
			}

			return this.items.filter((i) => i.selected);
		},

		someSelected() {
			return this.itemsSelected.length > 0;
		}
	},

	methods: {
		toggleSelectItem(item) {
			if (item.selected) {
				this.$set(item, 'selected', false);
			} else {
				this.$set(item, 'selected', true);
			}
		},

		toggleSelectAll(select) {
			for (let item of this.items) {
				this.$set(item, 'selected', select);
			}
		}
	}
};
</script>
