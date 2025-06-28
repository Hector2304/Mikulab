<template>
	<div>
		<b-table-simple class="m-0" small responsive>
			<b-thead>
				<b-tr>
					<b-th
						class="
							text-small text-center
							align-middle
							px-3
							py-1
							border-top-0 border-bottom-0
							cursor-pointer
						"
						@click="isMobile = !isMobile"
						nowrap
					>
						<b-icon :icon="isMobile ? 'phone-landscape' : 'phone'" class="mr-2"></b-icon>
						{{ isMobile ? 'Hora / Día' : 'Día / Hora' }}
					</b-th>
					<b-th
						v-for="(x, i) of xAxis"
						:key="`th-top-${i}`"
						class="
							text-small text-center
							align-middle
							flex-no-wrap
							border-left
							px-3
							py-1
							border-top-0 border-bottom-0
						"
						nowrap
					>
						{{ x.text }}
					</b-th>
				</b-tr>
			</b-thead>
			<b-tbody>
				<b-tr v-for="(y, i) of yAxis" :key="`tr-${i}`">
					<b-th class="text-small text-center align-middle px-3 py-1" nowrap>{{ y.text }}</b-th>
					<b-td
						v-for="(x, i) of xAxis"
						:key="`th-mid-${i}`"
						style="
							height: 40px;
							white-space: nowrap;
							text-overflow: ellipsis;
							overflow: hidden;
							max-width: 1px;
							position: relative;
						"
						class="border-left p-0 text-center align-middle"
						:class="sabadoClass(x, y)"
						@click.native.stop="doDefaultCellAction($event, x, y)"
						nowrap
					>
						<template v-if="isSelected(x, y)">
							<VueVar :payload="getPayload(x, y)" v-slot="{ payload }">
								<SchedulerCell
									:payload="payload"
									:day-hour="getDayHour(x, y)"
									@delete="deleteCell"
									@remove="unselect"
									showDelete
								/>
							</VueVar>
						</template>
						<template v-else-if="isUndoable(x, y)">
							<VueVar :payload="getPayload(x, y)" v-slot="{ payload }">
								<SchedulerCell
									:payload="payload"
									:day-hour="getDayHour(x, y)"
									@undelete="undeleteCell"
									@undo="undo"
									undoable
								/>
							</VueVar>
						</template>
						<template v-else-if="isPermanent(x, y)">
							<VueVar :payload="getPayload(x, y)" v-slot="{ payload }">
								<template v-if="payload && payload.type === 'REPORT'">
									<SchedulerReportCell :payload="payload" />
								</template>
								<template v-else>
									<SchedulerCell :payload="payload" :day-hour="getDayHour(x, y)"
									 @do-default="doDefaultCellAction($event, x, y)"/>
								</template>
							</VueVar>
						</template>
					</b-td>
				</b-tr>
			</b-tbody>
		</b-table-simple>
	</div>
</template>

<script>
export default {
	props: {
		schOptions: {
			type: Object,
			default: () => ({})
		}
	},

	data() {
		const hours = [];
		const formatHours = (i) => {
			let from;
			let to;
			let ii = i + 1;

			if (i < 10) {
				from = `0${i}`;
			} else {
				from = i;
			}

			if (ii < 10) {
				to = `0${ii}`;
			} else {
				to = ii;
			}

			return `${from}:00 - ${to}:00`;
		};

		for (let i = 7; i <= 21; i++) {
			hours.push({
				type: 'H',
				text: formatHours(i),
				value: i
			});
		}

		const weekDays = [];
		const createDay = (text, value) => {
			return {
				type: 'D',
				text,
				value
			};
		};

		weekDays.push(createDay('Lunes', 'L'));
		weekDays.push(createDay('Martes', 'M'));
		weekDays.push(createDay('Miércoles', 'MC'));
		weekDays.push(createDay('Jueves', 'J'));
		weekDays.push(createDay('Viernes', 'V'));
		weekDays.push(createDay('Sábado', 'S'));

		return {
			isMobile: true,
			hours,
			weekDays,
			permanent: {},
			selection: {},
			undoable: {},
			loading: {}
		};
	},

	computed: {
		xAxis() {
			if (this.isMobile) {
				return this.weekDays;
			} else {
				return this.hours;
			}
		},

		yAxis() {
			if (this.isMobile) {
				return this.hours;
			} else {
				return this.weekDays;
			}
		}
	},

	mounted() {
		// if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator?.userAgent)) {
		// 	this.isMobile = true;
		// } else {
		// 	this.isMobile = false;
		// }
	},

	methods: {
		getDayHour(x, y) {
			let d, h;

			if (x.type === 'D') {
				d = x;
				h = y;
			} else {
				h = x;
				d = y;
			}

			return { d, h };
		},

		getPayload(x, y) {
			let { d, h } = this.getDayHour(x, y);
			return (
				this.selection[`${d.value}${h.value}`]?.payload ||
				this.undoable[`${d.value}${h.value}`]?.payload ||
				this.permanent[`${d.value}${h.value}`]?.payload
			);
		},

		sabadoClass(x, y) {
			let { d, h } = this.getDayHour(x, y);

			if (d.value === 'S' && h.value >= 13) {
				return 'bg-disabled';
			}

			if (this.schOptions.noSelectable) {
				return '';
			}

			return 'scheduler-cell';
		},

		isPermanent(x, y) {
			let { d, h } = this.getDayHour(x, y);
			return !!this.permanent[`${d.value}${h.value}`];
		},

		isSelected(x, y) {
			let { d, h } = this.getDayHour(x, y);
			return !!this.selection[`${d.value}${h.value}`];
		},

		isUndoable(x, y) {
			let { d, h } = this.getDayHour(x, y);
			return !!this.undoable[`${d.value}${h.value}`];
		},

		isLoading(x, y) {
			let { d, h } = this.getDayHour(x, y);
			return !!this.loading[`${d.value}${h.value}`];
		},

		selectPermanent(d, h, p) {
			this.$set(this.permanent, `${d.value}${h.value}`, {
				day: d.value,
				hour: h.value,
				payload: p
			});
		},

		select(d, h, p) {
			this.$set(this.selection, `${d.value}${h.value}`, {
				day: d.value,
				hour: h.value,
				payload: p
			});
		},

		unselect(d, h, p) {
			this.$delete(this.selection, `${d.value}${h.value}`);

			this.$set(this.undoable, `${d.value}${h.value}`, {
				day: d.value,
				hour: h.value,
				payload: p
			});
		},

		undo(d, h, p) {
			this.$delete(this.undoable, `${d.value}${h.value}`);
			this.select(d, h, p);
		},

    doDefaultCellAction(e, x, y) {
      let { d, h } = this.getDayHour(x, y);

      if (d.value === 'S' && h.value >= 13) {
        return;
      }

      const payload = this.getPayload(x, y);

      this.$emit('default-cell-action', d, h, payload, (newPayload) => {
        this.select(d, h, newPayload);
      });
    },

		reset() {
			this.permanent = {};
			this.selection = {};
			this.undoable = {};
		},

		undeleteCell(d, h, p, callback) {
			this.$emit('undelete-cell', d, h, p, callback);
		},

		deleteCell(d, h, p, callback) {
			this.$emit('delete-cell', d, h, p, callback);
		}
	}
};
</script>

<style>
.scheduler-cell {
	cursor: pointer;
}
.scheduler-cell:hover {
	background-color: #b8c9d9;
}
</style>
