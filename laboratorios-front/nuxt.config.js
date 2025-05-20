export default {
	server: {
		host: '0.0.0.0'
	},

	// Target: https://go.nuxtjs.dev/config-target
	target: 'static',

	// Global page headers: https://go.nuxtjs.dev/config-head
	head: {
		title: 'Sistema de Gestión de Reservación de Laboratorios',
		htmlAttrs: {
			lang: 'es'
		},
		meta: [
			{ charset: 'utf-8' },
			{ name: 'viewport', content: 'width=device-width, initial-scale=1, shrink-to-fit=no' },
			{ hid: 'description', name: 'description', content: '' },
			{ name: 'format-detection', content: 'telephone=no' }
		],
		link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }]
	},

	// Global CSS: https://go.nuxtjs.dev/config-css
	css: ['~/assets/scss/unam-theme.scss'],

	// Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
	plugins: [],

	// Auto import components: https://go.nuxtjs.dev/config-components
	components: true,

	// Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
	buildModules: [
		[
			'@nuxtjs/moment',
			{
				defaultLocale: 'es',
				locales: ['es'],
				timezone: true,
				matchZones: 'America/Mexico_City',
				defaultTimezone: 'America/Mexico_City'
			}
		]
	],

	// Modules: https://go.nuxtjs.dev/config-modules
	modules: [
		// https://go.nuxtjs.dev/bootstrap
		[
			'bootstrap-vue/nuxt',
			{
				icons: true,
				bootstrapCSS: false,
				bootstrapVueCSS: false
			}
		],
		// https://go.nuxtjs.dev/axios
		'@nuxtjs/axios'
	],

	// Axios module configuration: https://go.nuxtjs.dev/config-axios
	axios: {
		// Workaround to avoid enforcing hard-coded localhost:3000: https://github.com/nuxt-community/axios-module/issues/308
		baseURL: '/'
	},

	// Build Configuration: https://go.nuxtjs.dev/config-build
	build: {
		// https://github.com/bootstrap-vue/bootstrap-vue/issues/5627
		babel: {
			compact: true
		}
	},

	loading: {
		color: '#003d79'
	},

	generate: {
		fallback: true
	},

	router: {
		prefetchLinks: false
	},

	env: {
		apiUrl: process.env.NODE_ENV === 'development' ? 'http://localhost:7070/api/' : '/api/'
	},

	plugins: [
		{ src: '~/plugins/axios-conf.js', mode: 'client' },
		{ src: '~/plugins/toasts.js', mode: 'client' },
		{ src: '~/plugins/utils.js', mode: 'client' }
	]
};
