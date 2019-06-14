<template>
    <div class="app-wrapper">
        <navbar></navbar>
        <router-view/>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import Navbar from "./components/Navbar";

@Component({components: {Navbar}})
export default class DashboardLayout extends Vue{
};
</script>

<style lang="scss" scoped>
</style>
