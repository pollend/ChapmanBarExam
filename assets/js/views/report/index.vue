<template>
    <div class="section">
        <div class="container" v-if="reports">
            <el-table :data="reports['hydra:member']">
                <el-table-column
                        prop="createdAt"
                        label="Created At">
                </el-table-column>
                <el-table-column
                        prop="submittedAt"
                        label="Submitted At">
                </el-table-column>
                <el-table-column label="Score" min-width="100px">
                    <template slot-scope="scope">
                        {{scope.row.score}} / {{scope.row.quiz.max_score}} ({{scope.row.score/scope.row.quiz.max_score}}%)
                    </template>
                </el-table-column>
                <el-table-column
                        prop="quiz.name"
                        label="Name">
                </el-table-column>
                <el-table-column label="View">
                    <template slot-scope="scope">
                        <el-button
                                size="mini"
                                @click="handleView(scope.row)">view</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import {namespace} from "vuex-class";
import User from "../../entity/user";
import service from "../../utils/request";
import {ExistFilter, FilterBuilder, SearchFilter} from "../../utils/filter";
import {HydraCollection} from "../../entity/hydra";
import QuizSession from "../../entity/quiz-session";

const authModule = namespace('auth')

@Component
export default class ReportList extends Vue {
    @authModule.Getter("user") user: User;
    @Provide() reports: HydraCollection<QuizSession> = null;

    handleView(row: any){
        this.$router.push({'name':'app.report.show','params':{'session_id': row.id}})
    }
    created() {
        service({
            url: '/_api/quiz_sessions?' + (new FilterBuilder()).addFilter(new ExistFilter('submittedAt',true)).addFilter(new SearchFilter("owner",this.user.id + '')).build(),
            method: 'GET'
        }).then((response) => {
            this.reports = response.data;
        }).catch((error) => {

        });
    }
}

</script>

<style>
</style>
