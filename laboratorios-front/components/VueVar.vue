<script>
// https://forum.vuejs.org/t/temporary-variable-in-a-template/10599/7
export default {
	render() {
		return this.$scopedSlots.default(this.$attrs);
	}
};
</script>
