<template>
    <div v-if="hydraCollection">
        <el-table v-loading="loading" :data="hydraCollection['hydra:member']">
            <el-table-column prop="name" label="Name">
            </el-table-column>
            <el-table-column label="Report" width="120">
                <template slot-scope="scope">
                    <el-button type="text" @click.native.prevent="viewReport(scope.row)" size="small">
                        View
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../../entity/classroom";
import service from "../../../../utils/request";
import {HydraCollection} from "../../../../entity/hydra";
import {Quiz} from "../../../../entity/quiz";
import {FilterBuilder, SearchFilter} from "../../../../utils/filter";

const classroomShowModule = namespace('dashboard/classroom/show');

@Component
export default class ClassesExams extends Vue {
    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() hydraCollection: HydraCollection<Quiz> = null;
    @Provide() loading: boolean = false;

    async loadCollection() {
        if(this.classroom) {
            this.loading = true;
            const builder = new FilterBuilder();
            builder.addFilter(new SearchFilter("quizSessions.classroom", this.classroom.id + ''));
            const response = await service({
                url: '/_api/quizzes?' + builder.build(),
                method: 'GET'
            });
            this.hydraCollection = response.data;
            this.loading = false;
        }
    }

    viewReport(row: Quiz){
        this.$router.push({
            'name':'dashboard.class.report.score_distribution.index',
            'params':{'class_id' : this.classroom.id+'', 'report_id' : row.id+''}})
    }

    async created() {
        await this.loadCollection();
    }
}

</script>
