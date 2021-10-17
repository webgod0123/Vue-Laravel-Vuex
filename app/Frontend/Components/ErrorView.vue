<template>
    <v-layout wrap>
        <v-flex xs12 px-5>
            <div v-show="error">
                <v-alert
                    color="red"
                    dense
                    dismissible
                    prominent
                    type="error">
                    <template v-if="isString()">
                        {{ error }}

                    </template>
                    <template v-else-if="isArray()">
                        <ul>
                             <li :key="item" 
                                v-for="item in error"
                                v-html="item">
                            </li>
                        </ul>
                    </template>
                </v-alert>                    
            </div>
        </v-flex>
    </v-layout>
</template>
<script lang="js">    
    export default {
        computed: {
            error: {
                cache: false,
                get() {
                    return this.$store.state.errorMessage;
                }
            }
        },
        methods: {
            onClearError() {
                this.$store.commit('clearError');
            },
            isArray() {
                return Array.isArray(this.$store.state.errorMessage);
            },
            isString() {
                return (typeof this.$store.state.errorMessage) === 'string';
            }
        }
    }
</script>