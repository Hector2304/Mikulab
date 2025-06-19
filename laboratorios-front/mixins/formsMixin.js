import { validationMixin } from 'vuelidate';

export default {
	mixins: [validationMixin],

	methods: {
		focusField(ref) {
			this.$refs[ref].focus();
		},

		validateState(form, field) {
			const { $dirty, $error } = this.$v[form][field];
			return $dirty ? !$error : null;
		}
	}
};
