
import eventBus from "@/common/eventBus";
const state = {
    filters: [],
    tableData: {
        records: {
            data: {}
        },
        columns: [],
        actions: [],
        tabFilters: [],
        massactions: [],
        paginationData: {
            has_pages: false
        }
    },
    groupStatesByCountries: [],
    countries: [],
    allSelected: false,
    sidebarFilter: false,
    selectedTableRows: [],
    filterData: {
        deal_amount_range: {
            label: 'Deal Amount Range',
            type: 'integer_range',
            values: ['1000', '50000'],
        },
        contact_person: {
            label: 'Contact Person',
            type: 'add',
            placeholder: 'Add Person',
            input_field_placeholder: 'Enter Person',
            values: ['Shubham', 'Webkul'],
        },
        date_range: {
            label: 'Date Range',
            type: 'date_range',
            values: ['2021-04-02', '2021-04-03'],
        },
        status: {
            label: 'Status',
            type: 'dropdown',
            placeholder: 'Select Status',
            values: ['Won', 'Lost'],
            options: ['Won', 'Lost']
        },
        phone_number: {
            label: 'Phone',
            type: 'add',
            placeholder: 'Add Number',
            input_field_placeholder: 'Enter Number',
            values: ['987654321', '987654321'],
        },
    },
    customTabFilter: false,
};

const mutations = {
    UPDATE_FILTER_VALUES(state, payload) {
        var key = payload?.key || null;

        if (key) {
            for (const filterKey in state.tableData.columns) {
                if (filterKey == key) {
                    key = state.tableData.columns[filterKey].index;
                    state.tableData.columns[filterKey].values = payload.values;
                }
            }
        }

        eventBus.emit('updateFilter', {
            key,
            value: payload?.values?.toString(),
            cond: payload?.condition || 'in',
        });
    },

    SELECT_ALL_ROWS(state, payload) {
        if (!(payload && state.selectedTableRows.length > 0)) {
            state.selectedTableRows = [];
            state.allSelected = payload || !state.allSelected;
            state.tableData.records.data.forEach(row => {
                if (state.allSelected) {
                    state.selectedTableRows.push(row.id);
                }
            });
        } else {
            state.selectedTableRows = [];
            state.allSelected = false;
        }
    },

    SELECT_TABLE_ROW(state, payload) {
        var isExisting = false;

        state.selectedTableRows.forEach((rowId, index) => {
            if (rowId == payload) {
                isExisting = true;
                state.selectedTableRows.splice(index, 1);
            }
        });

        if (!isExisting) {
            state.selectedTableRows.push(payload);
        }

        state.allSelected = (state.tableData.records.data.length == state.selectedTableRows.length);
    },

    UPDATE_TABLE_DATA(state, payload) {
        state.tableData = payload;
    },
    UPDATE_FILTERS(state, payload) {
        state.filters = payload;
    },
    TOGGLE_CUSTOM_TAB_FILTER(state, payload) {
        state.customTabFilter = payload;
    },
    UPDATE_GROUP_STATES_BY_COUNTRIES(state, payload) {
        state.groupStatesByCountries = payload
    },
    UPDATE_COUNTRIES(state, payload) {
        state.countries = payload
    }
};

const actions = {
    toggleSidebarFilter({ state }) {
        state.sidebarFilter = !state.sidebarFilter;
    },

    updateFilterValues({ commit }, payload) {
        commit('UPDATE_FILTER_VALUES', payload);
    },

    selectAllRows({ commit }, payload) {
        commit('SELECT_ALL_ROWS', payload);
    },

    selectTableRow({ commit }, payload) {
        commit('SELECT_TABLE_ROW', payload);
    },
    
    updateTableData({ commit }, payload) {
        commit('UPDATE_TABLE_DATA', payload);
    },
    updateFilters({ commit }, payload) {
        commit('UPDATE_FILTERS', payload);
    },
    toggleCustomTabFilter({commit}, payload) {
        commit('TOGGLE_CUSTOM_TAB_FILTER', payload);
    },
    updateStatesByCountries({commit}, payload) {
        commit('UPDATE_GROUP_STATES_BY_COUNTRIES', payload);
    },
    updateCountries({commit}, payload) {
        commit('UPDATE_COUNTRIES', payload);
    }

};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
};