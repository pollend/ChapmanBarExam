import {Sort} from "@/utils/filter";
<template>
    <div>
        <el-button-group>
            <el-button @click="updateFilter('BestAttempt')" type="primary">Best Attempt</el-button>
            <el-button @click="updateFilter('MostRecent')" type="primary">Most Recent</el-button>
            <el-button @click="updateFilter('Average')" type="primary">Average</el-button>
        </el-button-group>
        <p>Class Average: {{TotalAverage.toFixed(2)}}/{{quizMaxScore}} ({{(TotalAverage/quizMaxScore).toFixed(2)}})</p>
        <data-tables-server  layout="table" :loading="loading" :data="results">
            <el-table-column label="Score" prop="score"  min-width="100px">
                <template slot-scope="scope">
                    <template v-if="filter == 'BestAttempt'">
                        {{getBestAttempt(scope.row.sessions).toFixed(2)}} / {{ quizMaxScore}} ({{(getBestAttempt(scope.row.sessions)/ quizMaxScore).toFixed(2)}}%)
                    </template>
                    <template v-else-if="filter == 'MostRecent'">
                        {{getRecent(scope.row.sessions).toFixed(2)}} / {{ quizMaxScore}} ({{(getRecent(scope.row.sessions)/ quizMaxScore).toFixed(2)}}%)
                    </template>
                    <template v-else-if="filter == 'Average'">
                        {{getAverage(scope.row.sessions).toFixed(2)}} / {{ quizMaxScore}} ({{(getAverage(scope.row.sessions)/ quizMaxScore).toFixed(2)}}%)
                    </template>
                </template>
            </el-table-column>
            <el-table-column prop="user.email" label="Email"/>
            <el-table-column prop="user.username" label="User"/>
            <el-table-column label="Actions" min-width="100px">
                <template slot-scope="scope">
                    <el-button @click="handleView(scope.row)">View</el-button>
                </template>
            </el-table-column>
        </data-tables-server>
    </div>
</template>

<script lang="ts">

import {Component, Provide} from "vue-property-decorator";
import {mixins} from "vue-class-component";
import {HydraCollection, HydraMixxin} from "../../../../../entity/hydra";
import service from "../../../../../utils/request";
import {
    ExistFilter,
    FilterBuilder, ItemsPerPageFilter,
    SearchFilter,
    Sort,
    SortFilter
} from "../../../../../utils/filter";
import User from "../../../../../entity/user";
import _ from 'lodash'
import QuizSession from "../../../../../entity/quiz-session";
import * as moment from 'moment';

interface UserEntry {
    MaxScore: number;
    Score: number;
}

enum FilterRule {
    BestAttempt = "BestAttempt",
    Average = "Average",
    MostRecent = "MostRecent"
}

interface UserSessionPayload {
    user: User;
    sessions: QuizSession[];
}

@Component({
    components: {}
})
export default class UserReports  extends mixins(HydraMixxin){

    @Provide() payload: HydraCollection<UserSessionPayload> = null;
    @Provide() loading: boolean = false;
    @Provide() filter:FilterRule = FilterRule.BestAttempt

    get quizMaxScore(){
        return this.results.length > 0 ? this.results[0].sessions[0].quiz.max_score : 0
    }

    getBestAttempt(session: QuizSession[]){
        return _.max(_.map(session, (v) => v.score))
    }

    getAverage(session: QuizSession[]){
        return (+_.sum(_.map(session, (v) => v.score))/session.length)
    }

    getRecent(session: QuizSession[]){
        return (+_.orderBy(session,(o) => moment(o.submittedAt).fromNow())[0].score)
    }


    get TotalAverage(){
        if(this.payload) {
            var entr = _.map(this.results, (u) => {
                switch (this.filter) {
                    case FilterRule.BestAttempt:
                        return +this.getBestAttempt(u.sessions)
                    case FilterRule.MostRecent:
                        return +this.getRecent(u.sessions)
                    case FilterRule.Average:
                        return +this.getAverage(u.sessions)
                }
            });
            return (_.sum(entr)/entr.length)
        }
        return  0
    }

    get results() {
        return this.payload ? this.payload["hydra:member"] : []
    }

    async _loadUsers() {

        this.loading = true
        const builder = new FilterBuilder();
        builder.addFilter(new SearchFilter("quizSessions.classroom",this.$route.params.class_id));
        builder.addFilter(new SearchFilter("quizSessions.quiz",this.$route.params.report_id));
        builder.addFilter(new ItemsPerPageFilter(200));
        const response = await service({
            url: '/_api/users/classroom/' + this.$route.params.class_id + '/quiz/' + this.$route.params.report_id,
            method: 'GET'
        });
        this.payload = response.data
        this.loading = false
    }

    handleView(row: UserSessionPayload) {
        const report_id = this.$route.params.report_id
        const class_id = this.$route.params.class_id

        this.$router.push({
            'name': 'dashboard.class.report.students.standard',
            'params': {
                'class_id': class_id + '',
                'report_id': report_id + '',
                'user_id': row.user.id + '',
                'session_id': row.sessions[0].id + ''
            }
        })
    }

    updateFilter(rule:FilterRule) {
        if(rule != this.filter){
            this.filter = rule
        }
    }


    async created() {
        await this._loadUsers()
    }

}

</script>
