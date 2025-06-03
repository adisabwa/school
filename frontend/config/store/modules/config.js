const state = {
  pageTitle: 'Data Jadwal',
  pageSubTitle: 'Data Jadwal',
  pageSize:{
    width:0,
    height:0,
  }
};

const actions = {
  changePageTitle: (context, title) => {
    context.commit("PAGE_TITLE_UPDATE", title); 
  },
	
  changePageSubTitle: (context, subtitle) => {
    context.commit("PAGE_SUB_TITLE_UPDATE", subtitle); 
  },
  changePageSubTitle: (context, subtitle) => {
    context.commit("PAGE_SUB_TITLE_UPDATE", subtitle); 
  },
};

const mutations = {
  PAGE_SUB_TITLE_UPDATE: (state, subtitle) => {
    state.pageSubTitle = subtitle;
  },
  PAGE_TITLE_UPDATE: (state, title) => {
    state.pageTitle = title;
  }
 
};

const getters = {
  pageTitle: state => state.pageTitle,
  pageSubTitle: state => state.pageSubTitle,
	
};

export default {
	state,
	getters,
	actions,
	mutations,
};
