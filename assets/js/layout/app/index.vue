<template>
    <div class="app-wrapper">
        <navbar></navbar>
        <router-view/>
    </div>
</template>

<script lang="ts">

    import {Component, Vue} from "vue-property-decorator";
    import Navbar from "./components/Navbar";

    @Component({
        components: {Navbar}
    })
    export default class Layout extends Vue{

    }
</script>

<style lang="scss" scoped>
</style>