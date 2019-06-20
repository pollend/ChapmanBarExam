<template>
    <el-menu class="el-menu" mode="horizontal">
        <el-menu-item>
            <hamburger id="hamburger-container" :is-active="sidebarState" class="hamburger-container" @toggleClick="toggle()" />
        </el-menu-item>
        <el-submenu index="4" style="float: right;">
            <template slot="title"><i class="el-icon-menu"></i><template v-if="user">{{user.username}}</template></template>
            <!--      <el-menu-item index="4-1"><i class="el-icon-setting"></i> Settings</el-menu-item>-->
            <!--      <el-menu-item index="4-2"><i class="el-icon-information"></i> About</el-menu-item>-->
            <el-menu-item index="logout"><i class="el-icon-circle-close"></i> Logout</el-menu-item>
        </el-submenu>
        <el-menu-item>Dashboard</el-menu-item>
    </el-menu>

</template>

<script lang="ts">
import {Component, Vue} from "vue-property-decorator";
import Hamburger from '../../../components/Hamburger';
import {namespace} from "vuex-class";
import User from "../../../entity/user";

const dashboardSettingsModule = namespace('dashboard/settings');
const authModule = namespace('auth')

@Component({
    components: {Hamburger}
})
export default class Navbar extends Vue{
    @dashboardSettingsModule.Action("toggleLeftPanel") toggle: () => void;
    @dashboardSettingsModule.Getter("toggleLeftPanel") sidebarState: boolean;
    @authModule.Getter("user") user : User;

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
