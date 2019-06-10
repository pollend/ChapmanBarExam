<template>
  <div class="app-wrapper" :class="classObj">
    <div class="sidebar-container">
      <left-sidebar/>
    </div>
    <div class="main-container">
      <navbar />
      <router-view/>
    </div>
  </div>
</template>

<script>
  import {mapGetters, mapState} from 'vuex';
import { Navbar, LeftSidebar } from './components';

export default {
  name: 'Layout',
  components: {
    Navbar,
    LeftSidebar
  },
  computed: {
    ...mapGetters({
      'sidebarState': 'dashboard-setting/toggle_sidebar',
    }),
    classObj() {
      return {
        hideSidebar: this.sidebarState
      };
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">

  //sidebar
  $menuText:#bfcbd9;
  $menuActiveText:#409EFF;
  $subMenuActiveText:#f4f4f5; //https://github.com/ElemeFE/element/issues/12951

  $menuBg:#304156;
  $menuHover:#263445;

  $subMenuBg:#1f2d3d;
  $subMenuHover:#001528;

  $sideBarWidth: 210px;

  // Main container
  .main-container {
    min-height: 100%;
    transition: margin-left .28s;
    margin-left: $sideBarWidth;
    position: relative;
  }

  // Sidebar container
  .sidebar-container {
    transition: width 0.28s;
    width: $sideBarWidth !important;
    height: 100%;
    position: fixed;
    font-size: 0px;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1001;
    overflow: hidden;

    //reset element-ui css
    .horizontal-collapse-transition {
      transition: 0s width ease-in-out, 0s padding-left ease-in-out, 0s padding-right ease-in-out;
    }

    .scrollbar-wrapper {
      overflow-x: hidden !important;

      .el-scrollbar__view {
        height: 100%;
      }
    }

    .el-scrollbar__bar.is-vertical {
      right: 0px;
    }

    .is-horizontal {
      display: none;
    }

    a {
      display: inline-block;
      width: 100%;
      overflow: hidden;
    }

    .svg-icon {
      margin-right: 16px;
    }

    .el-menu {
      border: none;
      height: 100%;
      width: 100% !important;
    }

    // menu hover
    .submenu-title-noDropdown,
    .el-submenu__title {
      &:hover {
        background-color: $menuHover !important;
      }
    }

    .is-active > .el-submenu__title {
      color: $subMenuActiveText !important;
    }

    & .nest-menu .el-submenu > .el-submenu__title,
    & .el-submenu .el-menu-item {
      min-width: $sideBarWidth !important;
      background-color: $subMenuBg !important;

      &:hover {
        background-color: $subMenuHover !important;
      }
    }
  }

  .hideSidebar {
    .sidebar-container {
      width: 64px !important;
    }

    .main-container {
      margin-left: 64px;
    }

    .svg-icon {
      margin-right: 0px;
    }

    /*.submenu-title-noDropdown {*/
    /*  padding: 0 !important;*/
    /*  position: relative;*/

    /*  .el-tooltip {*/
    /*    padding: 0 !important;*/
    /*    .svg-icon {*/
    /*      margin-left: 20px;*/
    /*    }*/
    /*  }*/
    /*}*/

    /*.el-submenu {*/
    /*  overflow: hidden;*/

    /*  &>.el-submenu__title {*/
    /*    padding: 0 !important;*/
    /*    .svg-icon {*/
    /*      margin-left: 20px;*/
    /*    }*/

    /*    .el-submenu__icon-arrow {*/
    /*      display: none;*/
    /*    }*/
    /*  }*/
    /*}*/

    .el-menu--collapse {
      .el-submenu {
        &>.el-submenu__title {
          &>span {
            height: 0;
            width: 0;
            overflow: hidden;
            visibility: hidden;
            display: inline-block;
          }
        }
      }
    }
  }

  .app-wrapper {
    //@include clearfix;
    position: relative;
    height: 100%;
    width: 100%;

    /*&.mobile.openSidebar {*/
    /*  position: fixed;*/
    /*  top: 0;*/
    /*}*/
  }

  .drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
  }

  .fixed-header {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 9;
    width: calc(100% - #{$sideBarWidth});
    transition: width 0.28s;
  }

  /*.hideSidebar .fixed-header {*/
  /*  width: calc(100% - 54px)*/
  /*}*/

  .mobile .fixed-header {
    width: 100%;
  }
</style>
