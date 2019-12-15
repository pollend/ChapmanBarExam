<template>
    <el-tabs v-model="tab" tab-position="left" type="border-card" @tab-click="handleTab">
        <el-tab-pane v-for="(key,value) in tabs" :label="key" :name="value"></el-tab-pane>
        <template>
            <router-view/>
        </template>
    </el-tabs>

</template>

<script lang="ts">

    import {Component, Provide, Vue} from "vue-property-decorator";
    import {ElTabs} from "element-ui/types/tabs";
    import _ from "lodash"
    @Component
    export default class ExamReportContainer extends Vue {
        @Provide() tab: string = '';
        @Provide() tabs: {[key:string]: string}= {
            "dashboard.class.report.score_distribution": "Distribution",
            "dashboard.class.report.item_analysis": "Item Analysis",
            "dashboard.class.report.students": "Student Reports"
        }
        created() {
            for(var route in this.$router.currentRoute.matched){
                if(this.$router.currentRoute.matched[route].name in this.tabs){
                    this.tab =  this.$router.currentRoute.matched[route].name
                }
            }
        }

        handleTab(tab: any, event: string) {
            this.$router.push({
                name: tab.name + ".index",
                params: {class_id: this.$route.params.class_id, report_id: this.$route.params.report_id}
            })
        }
    }
</script>
