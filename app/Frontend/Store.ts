import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

export default new Vuex.Store({
    plugins: [ 
        createPersistedState({ 
            key: 'collect',
            paths: [ 'user', 'isLoggedIn' ]
        }) 
    ],
    state: {
        user: null,
        isLoggedIn: false,
        errorMessage: null,
        isLoading: false,
        modalContent: null,
        isDialogOpen: false,
        isFullScreen: false,
        dialogTitle: '',
        dialogProps: {},
        printPdfQueue: []
    },
    mutations: {
        loginUser(state: any, user: any) {
            state.user = user;
            state.isLoggedIn = true
        },
        logoutUser(state: any) {
            state.user = null;
            state.isLoggedIn = false
            localStorage.removeItem('collect') // important :)
        },
        clearError(state: any) {
            state.errorMessage = null
        },
        setError(state: any, error: any) {
            state.errorMessage = error
        },
        setIsLoading(state: any, value: Boolean) {
            state.isLoading = value
        },
        setDialogComponent(state: any, value: Boolean) {
            state.modalContent = value
        },
        setDialogTitle(state: any, value: string) {
            state.dialogTitle = value
        },
        setDialogProps(state: any, value: Object) {
            state.dialogProps = value
        },
        openDialog(state: any) {
            state.isDialogOpen = true
        },
        closeDialog(state: any) {
            state.isDialogOpen = false
        },
        toggleFullScreen(state: any, value: Boolean) {
            state.isFullScreen = value
        },
        pushToPrintQueue(state: any, value: Object) {
            state.printPdfQueue.push(value);
        },
        REMOVE_QUEUE(state: any, value: Object) {
            state.printPdfQueue = state.printPdfQueue.filter((item: any) => item.id !== (value as any).id);
        }
    },
    actions: {
        openDialog(context, {component, title, isFullScreen, dialogProps, dialogComponentEvents }) {
            return new Promise((resolve, reject) => {
                context.commit('toggleFullScreen',isFullScreen);
                context.commit('setDialogTitle',title);
                context.commit('openDialog');
                context.commit('setDialogProps',dialogProps);
                context.commit('setDialogComponent',component);
                return Promise.resolve();
            });
            // context.commit('toggleFullScreen',isFullScreen);
            // context.commit('setDialogTitle',title);
            // context.commit('openDialog');
            // context.commit('setDialogProps',dialogProps);
            // context.commit('setDialogComponent',component);
        },
        closeDialog(context) {
            context.commit('closeDialog');
            context.commit('setDialogTitle','');
            context.commit('setDialogProps',{});
            context.commit('setDialogComponent',null);
        }
    },
    modules: {},
    getters: {
        contractToPrint: (state, getters) => {
            return state.printPdfQueue;
            // if(state.printPdfQueue.length === 0) {
            //     return [];
            // }
            // return state.printPdfQueue;
        }
    }
})