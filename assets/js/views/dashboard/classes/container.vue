<template>
    <div class="section" v-if="classroom">
        <el-tabs v-model="tab"  @tab-click="handleTab" type="card">
            <el-tab-pane label="Overview" name="dashboard.class"></el-tab-pane>
            <el-tab-pane label="Report" name="dashboard.class.report"></el-tab-pane>
            <el-tab-pane label="Users" name="dashboard.class.user"></el-tab-pane>
            <el-tab-pane label="Whitelist" name="dashboard.class.whitelist"></el-tab-pane>
        </el-tabs>
        <router-view/>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";
    import Classroom from "../../../entity/classroom";
    import {namespace} from "vuex-class";
    import _ from "lodash";

    const classroomShowModule = namespace('dashboard/classroom/show')

    @Component
    export default class ShowClass extends Vue {
        @classroomShowModule.Getter('classroom') classroom: Classroom;
        @classroomShowModule.Action('query') query: (uid: number) => void;
        @Provide() tab: string = '';

        handleTab(tab: any, event: string) {
            this.$router.push({name: tab.name, params: {class_id: this.$route.params.class_id}})
        }

        async created() {
            this.tab = this.$router.currentRoute.name;
            await this.query(+this.$router.currentRoute.params['class_id']);
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
</style>
