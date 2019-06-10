<template>
  <el-menu @select="handleSelect" class="el-menu" mode="horizontal">
    <el-submenu index="4" style="float: right;">
      <template slot="title"><i class="el-icon-menu"></i>{{name}}</template>
<!--      <el-menu-item index="4-1"><i class="el-icon-setting"></i> Settings</el-menu-item>-->
<!--      <el-menu-item index="4-2"><i class="el-icon-information"></i> About</el-menu-item>-->
      <el-menu-item index="dashboard.home" :v-if="isAdmin"><i class="el-icon-s-management"></i> Admin</el-menu-item>
      <el-menu-item index="logout"><i class="el-icon-circle-close"></i> Logout</el-menu-item>
    </el-submenu>
    <el-menu-item>Chapman Bar Exam</el-menu-item>
    <el-menu-item index="app.home" v-if="!isSessionActive" >Exams</el-menu-item>
    <el-menu-item index="app.report" v-if="!isSessionActive" >Reports</el-menu-item>
  </el-menu>

</template>

<script>
  import { mapGetters } from 'vuex';
  import {ROLE_ADMIN} from "@/utils/role";

export default {
  components: {
  },
  computed: {
    ...mapGetters({
      'roles' : 'user/roles',
      'name' : 'user/username',
      'session_id': 'quiz-session/session_id'
    }),
    isAdmin(){
        return ROLE_ADMIN in this.roles
    },
    isSessionActive(){
        return this.session_id !== null;
    }
  },
  methods: {
    handleSelect(key, keyPath) {
      if(key === 'logout'){
        this.$store.dispatch('user/logout');
      }
      else {
        this.$router.push({name: key});
      }
    }
  },
};
</script>

<style lang="scss" scoped>

</style>
