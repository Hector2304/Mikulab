export const state = () => ({
	selectedCurso: undefined
});

export const mutations = {
	SET_SELECTED_CURSO(state, curso) {
		state.selectedCurso = curso;
	}
};

export const actions = {
	selectCurso({ commit }, curso) {
		commit('SET_SELECTED_CURSO', curso);
	},

	unselectCurso({ commit }) {
		commit('SET_SELECTED_CURSO', undefined);
	}
};
