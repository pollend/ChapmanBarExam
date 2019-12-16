<template>
    <div v-loading="breakdown === null"  class="breakdown" >
        <el-table
                :data="breakdown"
                style="width: 100%">
            <el-table-column
                    prop="tag"
                    label="Subtest Name"
                    width="400">
            </el-table-column>
            <el-table-column
                    prop="maxScore"
                    label="Possible Points"
                    width="180">
            </el-table-column>
<!--            <el-table-column-->
<!--                    prop="requiredToPass"-->
<!--                    label=" % Required To Pass">-->
<!--            </el-table-column>-->
            <el-table-column
                    prop="score"
                    label="Points Scored">
            </el-table-column>
            <el-table-column
                    prop="percentScore"
                    label="% Score">
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";
    import service from "../../utils/request";
    import NProgress from "nprogress";

    @Component
    export default class BreakdownReport extends Vue {
        @Provide() breakdown: any[] = [];

        async created() {
            NProgress.start();
            await service({
                url: '/_api/quiz_sessions/' + this.$router.currentRoute.params['session_id'] + '/breakdown',
                method: 'GET'
            }).then((response) => {
                NProgress.done();
                this.breakdown = response.data;
                this.breakdown.forEach((e) => {
                    // e.requiredToPass = '67.00%';
                    e.percentScore = parseFloat(((+e['score'] / +e['maxScore']) * 100.0) + '').toFixed(2) + "%";
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
