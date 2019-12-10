<template>
    <div>
        <el-tabs v-model="tab" tab-position="left" type="border-card" @tab-click="handleTab">
            <el-tab-pane label="Distribution" name="dashboard.class.report.score_distribution"><router-view/></el-tab-pane>
            <el-tab-pane label="Item Analysis" name="dashboard.class.report.item_analysis"><router-view/></el-tab-pane>
            <el-tab-pane label="Student Reports" name="dashboard.class.report.student_reports"><router-view/></el-tab-pane>
        </el-tabs>
    </div>
</template>

<script lang="ts">

    import {Component, Provide, Vue} from "vue-property-decorator";

    @Component
    export default class ExamReportContainer extends Vue {
        @Provide() tab: string = '';

        created() {
            this.tab = this.$router.currentRoute.name;
        }

        handleTab(tab: any, event: string) {
            this.$router.push({
                name: tab.name,
                params: {class_id: this.$route.params.class_id, report_id: this.$route.params.report_id}
            })
        }
    }
</script>
