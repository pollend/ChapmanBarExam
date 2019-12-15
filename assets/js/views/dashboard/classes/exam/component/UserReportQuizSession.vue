<template>
    <data-tables-server layout="table" :data="sessions" @query-change="loadCollection">
        <el-table-column label="Score" min-width="100px">
            <template slot-scope="scope">
                 {{scope.row.score}} / {{scope.row.quiz.max_score}} ({{scope.row.score/scope.row.quiz.max_score}}%)
            </template>
        </el-table-column>
        <el-table-column sortable="true" prop="score" label="Score"></el-table-column>
        <el-table-column sortable="true" prop="submittedAt" label="Submitted At"></el-table-column>
        <el-table-column label="Actions" min-width="100px">
            <template slot-scope="scope">
                <el-button @click="handleView(scope.row)">View</el-button>
            </template>
        </el-table-column>
    </data-tables-server>
</template>

<script lang="ts">
import {mixins} from "vue-class-component";
import {HydraCollection, HydraMixxin} from "../../../../../entity/hydra";
import {Component, Prop, Provide} from "vue-property-decorator";
import User from "../../../../../entity/user";
import service from "../../../../../utils/request";
import {ExistFilter, FilterBuilder, SearchFilter} from "../../../../../utils/filter";
import QuizSession from "../../../../../entity/quiz-session";
import Classroom from "../../../../../entity/classroom";
import {buildSortQueryForVueDataTable} from "../../../../../utils/vue-data-table-util";

@Component
export default class UserReportQuizSession extends mixins(HydraMixxin) {
    @Prop() user: User
    @Prop() class_id: string
    @Prop() report_id: string
    @Provide() collection: HydraCollection<QuizSession> = null;

    get sessions() {
        return this.collection ? this.collection["hydra:member"] : [];
    }

    handleView(row: QuizSession) {
        this.$router.push({
            'name': 'dashboard.class.report.student_report',
            'params': {
                'class_id': this.class_id + '',
                'report_id': this.report_id + '',
                'user_id': this.user.id + '',
                'session_id': row.id + ''
            }
        })
    }

    async loadCollection(query: {}) {
        const filter = buildSortQueryForVueDataTable(query);
        filter.addFilter(new ExistFilter('submittedAt', true))
        filter.addFilter(new SearchFilter("owner", this.user.id + ''))
        filter.addFilter(new SearchFilter("quiz",this.report_id + ''))
        filter.addFilter(new SearchFilter("classroom",this.class_id + ''))
        const response = await service({
            url: '/_api/quiz_sessions?' + filter.build(),
            method: 'GET'
        })
        this.collection = response.data
    }

}

</script>
