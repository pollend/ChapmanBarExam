<template>
    <div>
        <el-progress v-if="!result" :text-inside="true" :stroke-width="26" :percentage="progress"></el-progress>
        <el-divider v-if="!result" content-position="center">{{message}}</el-divider>

        <template v-if="result">
            <el-table :data="result" :set="cumm = 0">
                <el-table-column prop="score" label="Raw Score"></el-table-column>
                <el-table-column label="Percent Correct">
                    <template slot-scope="scope">
                        {{(scope.row.score/scope.row.maxScore) * 100.0}}%
                    </template>
                </el-table-column>
                <el-table-column prop="numberStudents" label="Number Of Students"></el-table-column>
                <el-table-column label="Percent Of Class">
                    <template slot-scope="scope">
                        {{(scope.row.numberStudents / totalStudents) * 100}}%
                    </template>
                </el-table-column>
                <el-table-column label="Cummulative Percent">
                    <template slot-scope="scope">
                        {{((cumm += scope.row.numberStudents) / totalStudents) * 100}}%
                    </template>
                </el-table-column>

            </el-table>
        </template>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../../entity/classroom";
import service from "../../../../utils/request";
import {Hydra, HydraCollection, HydraMixxin} from "../../../../entity/hydra";
import _ from "lodash";
import NProgress from "nprogress";
import { mixins } from "vue-class-component";

const classroomShowModule = namespace('dashboard/classroom/show');

interface Distribution{
    attempts: number,
    avgRawScore: number,
    maxRawScore: number,
    maxScore: number,
    user: string
}

@Component
export default class ScoreDistributionReport extends mixins(HydraMixxin) {
    @classroomShowModule.Getter('classroom') classroom: Classroom;

    @Provide() result: any[] = null;
    @Provide() totalStudents = 0;

    @Provide() message = '';
    @Provide() progress = 0;

    async _query(){
        NProgress.start();
        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/report/' + this.$route.params.report_id + '/distribution'
        });
        NProgress.done();

        let type = this.hydraType(response.data)
        if (type === 'hydra:Collection') {
            let payload: HydraCollection<Distribution> = response.data;
            let total = 0;
            let result: any[] = [];
            _.forEach(_.groupBy(payload["hydra:member"], function (v) {
                return v.maxRawScore;
            }), function (value, key) {
                result.push({
                    'numberStudents': value.length,
                    'maxScore': value[0].maxScore,
                    'score': value[0].maxRawScore,
                    'users': value.map(function (value) {
                        return value.user;
                    })
                })
            });

            this.totalStudents = _.reduce(result, function (result, value, key) {
                return value.numberStudents + result;
            }, 0);

            this.result = _.orderBy(result, function (v) {
                return -v.score;
            });
            return true;
        }
        return false;
    }

    async created() {
     if(await this._query() === false) {
         while (true) {
             this.message = "preparing"
             try {
                 await new Promise(resolve => setTimeout(() => resolve(), 1000));
                 const response = await service({
                     url: '/_api/classrooms/' + this.classroom.id + '/report/' + this.$route.params.report_id + '/distribution/status'
                 });
                 this.progress = response.data['percent']
                 this.message = response.data['message']
                 if (response.data['finished'] === true) {
                     break;
                 }

             } catch (e) {
                 return;
             }
         }
        await this._query()
     }


    }
}
</script>
