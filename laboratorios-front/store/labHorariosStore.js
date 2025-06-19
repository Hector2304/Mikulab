export const state = () => ({
	selectedLab: undefined
});

export const mutations = {
	SET_SELECTED_LAB(state, lab) {
		state.selectedLab = lab;
	}
};

export const actions = {
	selectLab({ commit }, lab) {
		commit('SET_SELECTED_LAB', lab);
	},

	unselectLab({ commit }) {
		commit('SET_SELECTED_LAB', undefined);
	}
};
