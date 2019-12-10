<template>
    <data-tables-server :data="sessions" @query-change="loadCollection">
        <el-table-column sortable="true" prop="score" label="Score"></el-table-column>
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

@Component
export default class UserReportQuizSession extends mixins(HydraMixxin) {
    @Prop() user: User
    @Prop() class_id: string
    @Prop() report_id: string
    @Provide() collection: HydraCollection<QuizSession> = null;

    get sessions() {
        return this.collection ? this.collection["hydra:member"] : [];
    }

    async loadCollection() {
        const response = await service({
            url: '/_api/quiz_sessions?' + (new FilterBuilder()).addFilter(new ExistFilter('submittedAt', true))
                .addFilter(new SearchFilter("owner", this.user.id + ''))
                .addFilter(new SearchFilter("quiz",this.report_id + '')).build(),
            method: 'GET'
        })
        this.collection = response.data
    }

}

</script>
