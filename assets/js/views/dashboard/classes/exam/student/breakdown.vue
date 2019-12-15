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
    import {Component, Provide, Watch} from "vue-property-decorator";
import {mixins} from "vue-class-component";
import {HydraMixxin} from "../../../../../entity/hydra";
    import NProgress from "nprogress";
    import service from "../../../../../utils/request";

@Component
export default class BreakdownReport  extends mixins(HydraMixxin) {
    @Provide() breakdown: any[] = [];

    @Watch('$route.params.session_id')
    async onSessionChange(value: string, oldValue: string) {
        this.breakdown = []
        await this.updateSession()
    }

    async updateSession(){
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
        NProgress.done()
    }

    async created() {
     await this.updateSession()
    }
}
</script>

<style>
    .breakdown{
        margin-bottom: 1rem;
    }
</style>
