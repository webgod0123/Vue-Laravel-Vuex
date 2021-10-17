<template>
    <v-dialog
        max-width="600"
        v-model="$store.state.isDialogOpen"
        :fullscreen="$store.state.isFullScreen"
        hide-overlay
        transition="dialog-bottom-transition">

        <div>
            <v-toolbar
                dark
                color="primary">

                <v-btn
                    icon
                    dark
                    v-on:click="$store.dispatch('closeDialog')">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                
                <v-toolbar-title>
                    {{ $store.state.dialogTitle }}
                </v-toolbar-title>

            </v-toolbar>

            <component v-bind:is="$store.state.modalContent"
                v-bind="$store.state.dialogProps" />
        </div>
    </v-dialog>
</template>