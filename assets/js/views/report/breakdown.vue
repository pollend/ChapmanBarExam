<template>
    <div v-loading="breakdown === null"  class="container breakdown" >
        <el-table
                :data="breakdown"
                style="width: 100%">
            <el-table-column
                    prop="tag.name"
                    label="Subtest Name"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="result.maxScore"
                    label="Possible Points"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="required_to_pass"
                    label=" % Required To Pass">
            </el-table-column>
            <el-table-column
                    prop="result.score"
                    label="Points Scored">
            </el-table-column>
            <el-table-column
                    prop="percent_score"
                    label="% Score">
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";
    import service from "../../utils/request";


    @Component
    export default class BreakdownReport extends Vue {
        @Provide() breakdown: any[] = [];

        async created(){
            await service({
              url:'/_api/quiz_sessions/'+  this.$router.currentRoute.params['report_id'] +'/breakdown',
                method:'GET'
            }).then((response) => {
                this.breakdown = response.data;
                this.breakdown.forEach((e) => {
                   e.required_to_pass = '67.00%';
                   e.percent_score = parseFloat(((+e['result']['score']/+e['result']['maxScore']) * 100.0) + '').toFixed(2)+"%";
                });
            }).catch((err) => {

            });
        }
    }
</script>

<style>
    .breakdown{
        margin-bottom: 1rem;
    }
</style>
