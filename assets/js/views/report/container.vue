<template>
    <div class="section">
        <el-tabs v-model="tab" @tab-click="handleReportTabs"  tab-position="left" type="border-card">
            <el-tab-pane label="Overview" name="app.report.show"><router-view/></el-tab-pane>
            <el-tab-pane label="Breakdown" name="app.report.breakdown"><router-view/></el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    import {getReports} from "@/api/report";
    import {mapGetters} from "vuex";

    export default {
        name: 'Report',
        data (){
          return {
              tab: this.$router.currentRoute.name
          };
        },
        methods: {
            handleReportTabs(tab, event) {
                this.$router.push({name: tab.name,params:{report_id:this.$route.params.report_id}})
            }
        }
    };
</script>

<style>
</style>
