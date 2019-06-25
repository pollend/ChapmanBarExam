<template>
    <el-menu
      :show-timeout="200"
      :default-active="$route.name"
      :collapse="sidebarState"
      @select="handleSelect"
      mode="vertical"
      background-color="#304156"
      text-color="#bfcbd9"
      active-text-color="#409EFF"
    >
      <el-menu-item index="dashboard.home">
            <i class="el-icon-s-home"></i>
            <span>Home</span>
      </el-menu-item>
        <el-submenu index="1">
            <template slot="title">
                <i class="el-icon-tickets"></i>
                <span slot="title">Users</span>
            </template>
        </el-submenu>
      <el-submenu index="2">
        <template slot="title">
          <i class="el-icon-notebook-2"></i>
          <span slot="title">Classes</span>
        </template>
        <el-menu-item index="dashboard.classes">All Classes</el-menu-item>
        <el-menu-item index="dashboard.classes.add">Add Class</el-menu-item>
      </el-submenu>
      <el-submenu index="3">
        <template slot="title">
            <i class="el-icon-tickets"></i>
            <span slot="title">Exams</span>
        </template>
        <el-menu-item index="dashboard.exam">All Exams</el-menu-item>
      </el-submenu>
    </el-menu>

</template>

<script lang="ts">
import {Component, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";

const dashboardSettingsModule = namespace('dashboard')

@Component
export default class LeftSidebar extends Vue {

    @dashboardSettingsModule.Getter("toggleLeftPanel") sidebarState: boolean;

    handleSelect(key: string, keyPath: string) {
        this.$router.push({name: key});
    }
}

</script>

<style rel="stylesheet/scss" lang="scss">

</style>
