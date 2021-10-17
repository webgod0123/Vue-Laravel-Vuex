<style scoped>
    .loading-overlay {
        background-color: grey; 
        opacity: 0.2; 
        position: absolute;
        top: 0; 
        left: 0; 
        right: 0;
        bottom: 0; 
    }

    .loading-indicator {
        position: fixed;
        top: 50%; 
        left: 50%;
        width: 100%;
        height: 100%;
        z-index: 99;
    }
</style>
<template>
    <div>
        <div v-if="$store.state.isLoading" class="loading-overlay"></div>

        <div v-if="$store.state.isLoading" class="loading-indicator">
            <v-progress-circular
                indeterminate
                :size="56"
                color="primary">
            </v-progress-circular>
        </div>
    </div>
</template>
<script>
export default {
 
}
</script>
