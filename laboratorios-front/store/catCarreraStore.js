import Vue from 'vue';

export const state = () => ({
	carreras: []
});

export const mutations = {
	SET_CARRERAS(state, { carreras }) {
		state.carreras = carreras;
	},

	ADD_CARRERA(state, { carrera }) {
		state.carreras.unshift(carrera);
	},

	EDIT_CARRERA(state, { carrera }) {
		Vue.set(state.carreras, state.carreras.indexOf(carrera), carrera);
	},

	REMOVE_CARRERA(state, { carrera }) {
		state.carreras.splice(state.carreras.indexOf(carrera), 1);
	}
};

export const actions = {};
