<template>
    <div v-loading="breakdown === null"  class="container breakdown" >
        <el-table
                :data="breakdown"
                style="width: 100%">
            <el-table-column
                    prop="subtest.name"
                    label="Subtest Name"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="max_score"
                    label="Possible Points"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="required_to_pass"
                    label=" % Required To Pass">
            </el-table-column>
            <el-table-column
                    prop="score"
                    label="Points Scored">
            </el-table-column>
            <el-table-column
                    prop="percent_score"
                    label="% Score">
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    import {getReportBreakdown} from "@/api/report";
    import  _ from 'lodash';
    export default {
        name: 'ReportBreakdown',
        components: { },
        data() {
            return {
                breakdown: null
            };
        },
        async created(){
            const response = await getReportBreakdown(this.$route.params.report_id);
            const {breakdown,tags} = response.data;
            console.log(breakdown);
            for (let key in breakdown) {
                breakdown[key].subtest = tags[key];
                breakdown[key].required_to_pass = '67.00%';
                breakdown[key].percent_score = parseFloat((breakdown[key].score/breakdown[key].max_score) * 100.0).toFixed(2)+"%"
            }
            this.breakdown = _.values(breakdown);
        },
        methods: {
        },
    };
</script>

<style>
    .breakdown{
        margin-bottom: 1rem;
    }
</style>
