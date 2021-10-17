<template>
    <v-layout align-center>
        <v-flex xs12 md4 offset-md4>
            <v-card class="pa-3" style="width: 100%;">
                <v-layout column>
                    <v-text-field :autofocus="true" v-model="email" label="Имейл" />

                    <v-text-field type="password" v-model="password" label="Парола" />

                    <v-btn v-on:click="onLogIn">
                        Вход
                    </v-btn>

                    <v-divider/>
                </v-layout>

                <div class="py-2">
                    <ErrorView/>
                </div>
                
            </v-card>
        </v-flex>

        <LoadingView/>

    </v-layout>
</template>

<script lang="js">
    import Network from '../Common/Network'
    import ErrorView from '../Components/ErrorView'
    import LoadingView from '../Components/LoadingView'

    export default {
        components: {
            ErrorView,
            LoadingView
        },
        created() {
            
        },
        mounted() {
           
        },
        mixins: [ Network ],
        data() {
            return {
                email: '',
                password: '',
            }
        },
        methods: {
            onLogIn() {
                this.request({
                    url: '/login',
                    method: 'POST',
                    data: {
                        email: this.email,
                        password: this.password,
                        
                    }
                }).then((response) => {
                    this.$store.commit('loginUser',response.user);
                    this.$router.push('/');
                })
            }
        }
    }
</script>
