

<template>
    <v-app>
        <template v-if="$store.state.isLoggedIn === false">
            <Login/>
        </template>
        <template v-else>
            <App/>
        </template>
    </v-app>
</template>

<script lang="js">
    import Login from './Pages/Login'
    import App from './Pages/App'
    
    export default {
        components: {
            Login,
            App
        } 
    }
</script>