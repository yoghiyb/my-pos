const state = () => ({
    user: null
});

const mutations = {
    SET_USER(state, payload) {
        state.user = payload
    }
}

const actions = {
    getUserAuth({ commit }) {
        let endpoint = `${BASE_URL}/user`
        axios.get(endpoint).then(response => {
            commit('SET_USER', response.data.data)
        }).catch(err => console.log(err));
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
