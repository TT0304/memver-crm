import Vuex from 'vuex';

import layout from './modules/layout';
import notification from './modules/layout';
import todo from './modules/todo';
import data from './modules/data';


const store = new Vuex.Store({
  modules: {
    layout: layout, // Register the layout module
    notification, // Register the notifications module
    todo,
    data,
    // Add more modules as needed
  },
});

export default store;

