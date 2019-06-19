<template>
    <div class="section">
        <el-tabs v-model="tab" @tab-click="handleReportTabs"  tab-position="left" type="border-card">
            <el-tab-pane label="Overview" name="app.report.show"><router-view/></el-tab-pane>
            <el-tab-pane label="Breakdown" name="app.report.breakdown"><router-view/></el-tab-pane>
        </el-tabs>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";

    @Component
    export default class ReportContainer extends Vue{
        @Provide() tab: string = this.$router.currentRoute.name;

        handleReportTabs(tab: any, event: string) {
            this.$router.push({name: tab.name, params: {report_id: this.$route.params.report_id}})
        }
    }
</script>

<style>
</style>
