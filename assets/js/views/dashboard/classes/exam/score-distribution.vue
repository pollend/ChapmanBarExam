<template>
    <div v-if="result">
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
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../../entity/classroom";
import service from "../../../../utils/request";
import {HydraCollection} from "../../../../entity/hydra";
import _ from "lodash";
import NProgress from "nprogress";

const classroomShowModule = namespace('dashboard/classroom/show');

interface Distribution{
    attempts: number,
    avgRawScore: number,
    maxRawScore: number,
    maxScore: number,
    user: string
}

@Component
export default class ScoreDistributionReport extends Vue {
    @classroomShowModule.Getter('classroom') classroom: Classroom;

    @Provide() result: any[] = null;
    @Provide() totalStudents = 0;

    async created() {

        NProgress.start();
        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/report/' + this.$route.params.report_id + '/disribution'
        });
        NProgress.done();
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

    }
}
</script>