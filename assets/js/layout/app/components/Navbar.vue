<template>
  <el-menu @select="handleSelect" class="el-menu" mode="horizontal">
    <el-submenu index="4" style="float: right;">
      <template slot="title"><i class="el-icon-menu"></i>{{user.username}}</template>
      <!--      <el-menu-item index="4-1"><i class="el-icon-setting"></i> Settings</el-menu-item>-->
      <!--      <el-menu-item index="4-2"><i class="el-icon-information"></i> About</el-menu-item>-->

      <el-menu-item index="dashboard.home" :disabled="!containsRole('ROLE_ADMIN')"><i class="el-icon-s-management"></i> Admin</el-menu-item>
      <el-menu-item index="logout"><i class="el-icon-circle-close"></i> Logout</el-menu-item>
    </el-submenu>
    <el-menu-item>Chapman Bar Exam</el-menu-item>
    <el-menu-item index="app.home" v-if="!session" >Exams</el-menu-item>
    <el-menu-item index="app.report" v-if="!session" >Reports</el-menu-item>
  </el-menu>

</template>

<script lang="ts">
  import {Component, Vue} from "vue-property-decorator";
  import "vue-router";
  import {namespace, Getter} from "vuex-class";
  import User from "../../../entity/user";
  import QuizSession from "../../../entity/quiz-session";

  const authModule = namespace('auth')
  const sessionModule = namespace('app/user-quiz-session')

  @Component
  export default class Navbar extends Vue {
    @authModule.Getter("roles") roles: string[];
    @authModule.Getter("user") user : User;
    @authModule.Action("logout") logout: () => void;
    @sessionModule.Getter("session") session: QuizSession;

    containsRole(role: string) {
      for(const r of this.roles){
        if(r == role)
          return true;
      }
      return false;
    }

    handleSelect(key: string, keyPath: string) {
      if (key === 'logout') {
        this.logout();
        this.$router.push({name: 'app.login'})
      } else {
        this.$router.push({name: key});
      }
    }
  }

</script>

<style lang="scss" scoped>
</style>