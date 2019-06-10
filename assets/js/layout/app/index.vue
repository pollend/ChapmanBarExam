<template>
    <div class="app-wrapper">
        <navbar></navbar>
        <router-view/>
    </div>
</template>

<script>
import { Navbar } from './components';
export default {
  name: 'Layout',
  components: {
    Navbar
  },
  mixins: [],
  computed: {
  },
  methods: {
  },
};
</script>

<style lang="scss" scoped>
</style>
