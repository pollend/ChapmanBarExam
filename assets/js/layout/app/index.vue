<template>
    <div class="app-wrapper">
        <navbar></navbar>
        <app-main />
    </div>
</template>

<script>
import { Navbar, AppMain } from './components';
export default {
  name: 'Layout',
  components: {
    Navbar,
    AppMain,
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
