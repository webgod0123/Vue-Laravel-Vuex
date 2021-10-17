<template>
    <v-snackbar
        :color="color"
        :timeout="timer"
        v-model="showSnackbar"
        bottom
        center>
        <v-icon left>{{icon}}</v-icon>
        
        {{message}}

        <template v-slot:action="{ attrs }">
            <v-btn
                text
                small
                v-bind="attrs"
                v-on:click="showSnackbar = false">
            Затвори
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    name: "Toast",
    data() {
        return{
            showSnackbar: false,
            message: 'Успех',
            color: 'success',
            icon: 'mdi-check',
            timer: 3000
        }
    },
    methods:{
        show(data) {
            this.message = data.message ?? this.message;
            this.color = data.color ?? this.color;
            this.timer = data.timer ?? this.timer;
            this.icon = data.icon ?? this.icon;
            this.showSnackbar = true;
        }
    }
}
</script>