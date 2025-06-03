
const state = {
	// baseUrl: '/ujian/'
	filter: {
		semester: null,
		prodi: null,
		peminatan:'',
		ujian:'',
		keyowrd:'',
	}
};

const actions = {
	resetFilter: (context) => {
		context.commit("FILTER_UPDATE", 
			{
				semester: null,
				prodi: null,
				peminatan:'',
				ujian:'',
				keyowrd:'',
			});
	},
	saveFilter: (context, {ref, data}) => {
		context.commit("FILTER_UPDATE", 
			{
				ref: ref,
				data: data,
			});
	}
};

const mutations = {
	FILTER_UPDATE: (state, {ref, data}) => {
	    state.filter[ref] = data;
	},
};

const getters = {
	filter: state => state.filter,
};

export default {
	state,
	getters,
	actions,
	mutations,
};
