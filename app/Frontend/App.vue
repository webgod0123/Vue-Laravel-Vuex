<template>
    <v-app>
        <v-navigation-drawer v-if="$store.state.isLoggedIn"
            app
            v-model="isDrawerOpen"
            clipped
            left
            v-bind:style="{'margin-top': oldBrowser ? '50px' : ''}">

            <template v-slot:prepend>
                <v-list-item two-line>
                    <v-avatar
                        color="primary"
                        size="36">
                        <span class="white--text">
                            {{ $store.state.user.name.charAt(0) }}
                        </span>
                    </v-avatar>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ $store.state.user.name }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            {{ $store.state.user.email }}
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </template>

            <v-divider></v-divider>
                
            <v-list>
                <!-- reports -->
                <v-list-group
                    value="true"
                    prepend-icon="mdi-chart-line"
                    no-action>
                    <template v-slot:activator>
                        <v-list-item-content>
                            <v-list-item-title>
                                Справки
                            </v-list-item-title>
                        </v-list-item-content>
                    </template>

                    <v-list-item
                        v-on:click="$router.push('/')">
                        <v-list-item-content>
                            <v-list-item-title>
                                Трудови договори
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-on:click="$router.push('/obligation')">
                        <v-list-item-content>
                            <v-list-item-title>
                                Задължения
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
                   
                <!-- admins -->
                <v-list-item
                    key="1"
                    v-on:click="$router.push('admins')">
                    <v-list-item-icon>
                        <v-icon>mdi-information-outline</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>
                            Потребители
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                
                <v-divider></v-divider>

                <v-list-item 
                    v-ripple 
                    v-on:click="onLogout">
                    <v-list-item-icon>
                        <v-icon color="red">mdi-power</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Exit</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>

            <v-overlay :value="$store.state.isLoading"></v-overlay>

        </v-navigation-drawer>
        
        <v-app-bar
            app 
            dense
            clipped-left>
            
            <v-app-bar-nav-icon v-if="$store.state.isLoggedIn" v-on:click="isDrawerOpen = !isDrawerOpen"></v-app-bar-nav-icon>

            <v-layout>
                <v-layout grow justify-center align-center>
                    <span class="headline">
                        ЧСИ Неделчо Митев
                    </span>
                </v-layout>
            </v-layout>
        </v-app-bar>

        <v-main v-bind:style="{'margin-top': oldBrowser ? '50px' : ''}">
            <v-container fluid :class="{'fill-height': !$store.state.isLoggedIn }">
                <keep-alive v-if="$store.state.isLoggedIn">
                    <router-view></router-view>
                </keep-alive>
                <router-view v-else></router-view>
                    
                <!-- <div v-if="$store.state.isLoading">
                    <v-overlay :value="true">
                        <v-progress-circular
                            indeterminate
                            size="64">
                        </v-progress-circular>  
                    </v-overlay>
                </div> -->
            
            </v-container>
        </v-main>

        <Toast ref="toast"/>

        <LoadingView/>
    </v-app>
</template>

<script lang="js">
    import Network from './Common/Network'
    import LoadingView from './Components/LoadingView'
    import Toast from './Components/Toast'

    export default {
        components: {
            LoadingView,
            Toast
        },
        mixins: [ Network ],
        created() {
            this.$store.commit('clearError');
            // if(window.isDevMode) {
            //     this.$store.commit('loginUser',{
            //         id: 1,
            //         is_admin: 1,
            //         name: 'John Doe',
            //         email: 'email@example.com',
            //     });
            //     this.$router.push('/');
            // }
        },
        mounted() {
            this.$root.toast = this.$refs.toast
            this.oldBrowser = typeof Object.values != 'function'
        },
        data() {
            return {
                overlay: false,
                oldBrowser: false,
                selectedItem: 0,
                isDrawerOpen: this.$isMobile() === false,
            }
        },
        methods: {
            
        }
    }
</script>