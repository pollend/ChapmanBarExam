<template>
  <el-menu @select="handleSelect" class="el-menu" mode="horizontal">
    <el-submenu index="4" style="float: right;">
      <template slot="title"><i class="el-icon-menu"></i></template>
      <el-menu-item index="4-1"><i class="el-icon-setting"></i> Settings</el-menu-item>
      <el-menu-item index="4-2"><i class="el-icon-information"></i> About</el-menu-item>
      <el-menu-item index="logout"><i class="el-icon-circle-close"></i> Logout</el-menu-item>
    </el-submenu>
    <el-menu-item>Chapman Bar Exam</el-menu-item>
    <el-menu-item index="app.home" >Exams</el-menu-item>
    <el-menu-item index="app.reports">Reports</el-menu-item>
  </el-menu>

</template>

<script>
export default {
  components: {
  },
  computed: {
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
