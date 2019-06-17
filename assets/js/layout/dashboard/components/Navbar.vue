<template>
    <el-menu class="el-menu" mode="horizontal">
        <el-menu-item>
            <hamburger id="hamburger-container" :is-active="sidebarState" class="hamburger-container" @toggleClick="toggleSideBar" />
        </el-menu-item>
        <el-submenu index="4" style="float: right;">
            <template slot="title"><i class="el-icon-menu"></i>{{name}}</template>
            <!--      <el-menu-item index="4-1"><i class="el-icon-setting"></i> Settings</el-menu-item>-->
            <!--      <el-menu-item index="4-2"><i class="el-icon-information"></i> About</el-menu-item>-->
            <el-menu-item index="logout"><i class="el-icon-circle-close"></i> Logout</el-menu-item>
        </el-submenu>
        <el-menu-item>Dashboard</el-menu-item>
    </el-menu>

</template>

<script lang="ts">
import {mapGetters} from 'vuex';
import {Component, Vue} from "vue-property-decorator";
import Hamburger from '../../../components/Hamburger';

@Component({
    components: {Hamburger}
})
export default class Navbar extends Vue{

    async logout() {
        await this.$store.dispatch('user/logout');
        this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    }
};

//
//
// computed: {
// ...mapGetters({
//         'sidebarState': 'dashboard-setting/toggle_sidebar',
//         'name' : 'user/username',
//     })
// },
// methods: {
//     toggleSideBar() {
//         this.$store.dispatch('dashboard-setting/toggleSidebar');
//     },
//     async logout() {
//         await this.$store.dispatch('user/logout');
//         this.$router.push(`/login?redirect=${this.$route.fullPath}`);
//     },
// },
</script>

<style lang="scss" scoped>

</style>
