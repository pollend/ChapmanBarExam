import {Sort} from "@/utils/filter";
<template>
    <div>
        <el-button-group>
            <el-button @click="updateFilter('BestAttempt')" type="primary">Best Attempt</el-button>
            <el-button @click="updateFilter('MostRecent')" type="primary">Most Recent</el-button>
        </el-button-group>
        <data-tables-server  layout="table" :loading="loading" :data="results">
            <el-table-column label="Score" prop="score"  min-width="100px">
                <template slot-scope="scope">
                    {{scope.row.score}} / {{scope.row.quiz.max_score}} ({{scope.row.score/scope.row.quiz.max_score}}%)
                </template>
            </el-table-column>
            <el-table-column prop="owner.email" label="Email"></el-table-column>
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
    FilterBuilder,
    ItemsPerPageFilter,
    SearchFilter,
    Sort,
    SortFilter
} from "../../../../../utils/filter";
import User from "../../../../../entity/user";
import _ from 'lodash'
import QuizSession from "../../../../../entity/quiz-session";

interface UserEntry {
    MaxScore: number;
    Score: number;
}

enum FilterRule {
    BestAttempt = "BestAttempt",
    MostRecent = "MostRecent"
}

@Component({
    components: {}
})
export default class UserReports  extends mixins(HydraMixxin){

    @Provide() users: HydraCollection<User> = null;
    @Provide() loading: boolean = false;
    @Provide() filter:FilterRule = FilterRule.BestAttempt
    @Provide() results: QuizSession[] = []

    async _loadUsers() {

        const builder = new FilterBuilder();
        builder.addFilter(new SearchFilter("classes",this.$route.params.class_id));
        const response = await service({
            url: '/_api/users?' + builder.build(),
            method: 'GET'
        });
        this.users = response.data
        await this.refreshSessions()
    }

    handleView(row: QuizSession) {
        const report_id = this.$route.params.report_id
        const class_id = this.$route.params.class_id

        this.$router.push({
            'name': 'dashboard.class.report.students.standard',
            'params': {
                'class_id': class_id + '',
                'report_id': report_id + '',
                'user_id': row.owner.id + '',
                'session_id': row.id + ''
            }
        })
    }

    updateFilter(rule:FilterRule) {
        if(rule != this.filter){
            this.filter = rule
            this.refreshSessions()
        }
    }

    async refreshSessions () {
        this.loading = true
        const report_id = this.$route.params.report_id
        const class_id = this.$route.params.class_id

        const quizSessions: QuizSession[] = await Promise.all(_.map(this.users["hydra:member"],async (value: User): Promise<QuizSession> => {
            const filter = new FilterBuilder();
            filter.addFilter(new ExistFilter('submittedAt', true))
            filter.addFilter(new SearchFilter("owner", value.id + ''))
            filter.addFilter(new SearchFilter("quiz",report_id + ''))
            filter.addFilter(new SearchFilter("classroom",class_id + ''))
            filter.addFilter(new ItemsPerPageFilter(1));
            switch (this.filter) {
                case FilterRule.BestAttempt:
                    filter.addFilter(new SortFilter("score",Sort.Descending))
                    break
                case FilterRule.MostRecent:
                    filter.addFilter(new SortFilter("submittedAt",Sort.Descending))
                    break
            }
            const response = await service({
                url: '/_api/quiz_sessions?' + filter.build(),
                method: 'GET'
            })
            const sessions: HydraCollection<QuizSession> = response.data
            if(sessions["hydra:member"].length > 0){
                return sessions["hydra:member"][0]
            }
            return null
        }))
        this.results = _.filter(quizSessions,(o) => o != null)
        this.loading = false

    }

    async created() {
        await this._loadUsers()
    }

}

</script>
