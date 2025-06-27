export default function (context) {
	context.$axios.setBaseURL(process.env.apiUrl);

	context.$axios.onRequest((config) => {
		config.withCredentials = true;

		return config;
	});

	context.$axios.onResponseError((error) => {
		if (error.response && error.response.status === 401) {
			context.redirect('/');
		} else if (error.response && error.response.status === 403) {
			context.$toastError({
				title: 'Error',
				text: 'Sin permisos.'
			});
		} else {
			context.$toastError({
				title: 'Error',
				text: error?.response?.status ?? '',
				delay: 1000
			});
			console.dir(error);
		}

		return Promise.reject(error);
	});
}
