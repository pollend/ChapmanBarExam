<template>
    <div class="container">
        <el-tabs v-model="tab" type="card" @tab-click="handleReportTabs" >
            <el-tab-pane label="Overview" name="app.report.show"/>
            <el-tab-pane label="Breakdown" name="app.report.breakdown"/>
        </el-tabs>
        <router-view/>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";

    @Component
    export default class ReportContainer extends Vue{
        @Provide() tab: string = '';

        created(){
            this.tab = this.$router.currentRoute.name;
        }

        handleReportTabs(tab: any, event: string) {
            this.$router.push({name: tab.name, params: {report_id: this.$route.params.report_id}})
        }
    }
</script>

<style>
    .container {
        margin-top: 2rem;
    }
</style>
