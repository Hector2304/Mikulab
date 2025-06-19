export const state = () => ({
	isSessionActive: false,
	personName: undefined,
	userType: undefined,
	username: undefined
});

export const mutations = {
	SET_SESSION_ACTIVE(state, isIt) {
		state.isSessionActive = isIt;
	},

	SET_PERSON_NAME(state, pName) {
		state.personName = pName;
	},

	SET_USER_TYPE(state, type) {
		state.userType = type;
	},

	SET_USERNAME(state, uname) {
		state.username = uname;
	},

	DO_REDIRECT(state, to) {
		this.$router.push(to);
	}
};

export const actions = {
	/**
	 *
	 */
	login({ commit }, payload) {
		return this.$axios.post('controller/sesion.php', payload).then((response) => {
			commit('SET_SESSION_ACTIVE', true);
			commit('SET_PERSON_NAME', response.data.personName);
			commit('SET_USER_TYPE', response.data.userType);
			commit('SET_USERNAME', response.data.username);

			if (response.data.userType === 'PROFESOR') {
				commit('DO_REDIRECT', '/profesor');
			} else if (response.data.userType === 'SUPERUSUARIO') {
				commit('DO_REDIRECT', '/superusuario');
			} else if (response.data.userType === 'TECNICO') {
				commit('DO_REDIRECT', '/tecnico');
			} else if (response.data.userType === 'SERVIDOR_SOCIAL') {
				commit('DO_REDIRECT', '/servidor-social');
			}  else if (response.data.userType === 'VIGILANTE') {
        commit('DO_REDIRECT', '/tecnico');
    }

    return response;
		});
	},

	/**
	 *
	 */
	getSession({ commit, dispatch }) {
		return this.$axios
			.get('controller/sesion.php')
			.then((response) => {
				commit('SET_SESSION_ACTIVE', true);
				commit('SET_PERSON_NAME', response.data.personName);
				commit('SET_USER_TYPE', response.data.userType);
				commit('SET_USERNAME', response.data.username);

				if (response.data.userType === 'PROFESOR') {
					commit('DO_REDIRECT', '/profesor');
				} else if (response.data.userType === 'SUPERUSUARIO') {
					commit('DO_REDIRECT', '/superusuario');
				} else if (response.data.userType === 'TECNICO') {
					commit('DO_REDIRECT', '/tecnico');
				} else if (response.data.userType === 'SERVIDOR_SOCIAL') {
					commit('DO_REDIRECT', '/servidor-social');
				} else if (response.data.userType === 'VIGILANTE') {
          commit('DO_REDIRECT', '/tecnico');
        }
				return response;
			})
			.catch((error) => {
				if (error?.response?.status === 401) {
					dispatch('resetSession');
				}
			});
	},

	/**
	 *
	 */
	logout({ dispatch }) {
		return this.$axios.delete('controller/sesion.php').then((response) => {
			dispatch('resetSession');
			return response;
		});
	},

	/**
	 *
	 */
	resetSession({ commit }) {
		commit('SET_SESSION_ACTIVE', false);
		commit('SET_PERSON_NAME', undefined);
		commit('SET_USER_TYPE', undefined);
		commit('SET_USERNAME', undefined);
		commit('DO_REDIRECT', '/');
	}
};
