export default (context, inject) => {
	const ttoast = (opts = {}) => {
		const $bvt = $nuxt?.$bvToast ?? window?.$nuxt?.$bvToast;
		if (!$bvt) {
			console.log('undefined toast');
			return;
		}

		$bvt.toast(opts.text ?? 'Listo', {
			title: opts.title ?? 'Listo',
			toaster: opts.toaster ?? 'b-toaster-top-right',
			variant: opts.variant,
			autoHideDelay: opts.delay ?? 5000,
			solid: true
		});
	};

	// secondary
	// warning
	// info

	// Primary
	inject('toastPrimary', (opts = {}) => {
		ttoast({ ...opts, variant: 'primary' });
	});

	// Success
	inject('toastSuccess', (opts = {}) => {
		ttoast({ ...opts, variant: 'success' });
	});

	// Error
	inject('toastError', (opts = {}) => {
		ttoast({ ...opts, variant: 'danger' });
	});
};
