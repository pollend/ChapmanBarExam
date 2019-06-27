<template>
    <div>
        <el-form ref="form" v-if="classroom">
            <el-form-item>
                <data-tables-server class="quiz-access-table" :total="totalItems"  :table-props="tableProps"  :loading="loading" :data="items" @query-change="loadData">
                    <el-table-column type="selection" width="55">
                    </el-table-column>
                    <el-table-column label="Open" width="60">
                        <template slot-scope="scope">
                            <el-icon v-if="scope.row.isOpen" class="el-icon-s-opportunity"></el-icon>
                        </template>
                    </el-table-column>

                    <el-table-column prop="openDate" label="Open And Close" width="460">
                        <template slot-scope="scope">
                            <el-date-picker
                                    @change="rowChange(scope.row)"
                                    v-model="scope.row.range"
                                    type="datetimerange"
                                    start-placeholder="Start Date"
                                    end-placeholder="End Date">
                            </el-date-picker>
                        </template>
                    </el-table-column>
                    <!--            <el-table-column prop="close_date" label="Close Date"></el-table-column>-->
                    <el-table-column prop="isHidden" label="Hide" width="70">
                        <template slot-scope="scope">
                            <el-checkbox change="rowChange(scope.row)"  @change="rowChange(scope.row)"   v-model="scope.row.isHidden"></el-checkbox>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="Exam">
                        <template slot-scope="scope">
                            <exam-search @change="rowChange(scope.row)" v-model="scope.row.quiz"></exam-search>
                        </template>
                    </el-table-column>
                    <el-table-column prop="numAttempts" label="Attempt Count" >
                        <template slot-scope="scope">
                            <el-input-number v-model="scope.row.numAttempts" @change="rowChange(scope.row)"  :min="0" ></el-input-number>
                        </template>
                    </el-table-column>
                    <el-table-column>
                        <template slot-scope="scope">
                            <el-button type="primary" icon="el-icon-delete" @click="handleDeleteAccess(scope.row)"></el-button>
                        </template>
                    </el-table-column>
                </data-tables-server>
            </el-form-item>
            <el-form-item>
                <el-button-group style="float:right" >
                    <el-button @click="showCreate = !showCreate">Add</el-button>
                    <el-button :disabled="!hasMarked" @click="onSubmit">Save</el-button>
                </el-button-group>
            </el-form-item>
        </el-form>

        <el-dialog :visible.sync="showCreate" title="Create Quiz Access">
            <create-quiz-access-form :classroom="classroom" @submit="handleSubmitCreate" @cancel="showCreate = false"></create-quiz-access-form>
        </el-dialog>
    </div>
</template>

<script lang="ts">

import Classroom from "../../../../entity/classroom";
import {Component, Prop, Provide, Vue, Watch} from "vue-property-decorator";
import {HydraCollection, hydraID, HydraMixxin} from "../../../../entity/hydra";
import QuizAccess from "../../../../entity/quiz-access";
import service from "../../../../utils/request";
import {buildSortQueryForVueDataTable} from "../../../../utils/vue-data-table-util";
import {SearchFilter} from "../../../../utils/filter";
import ExamSearch from './ExamSearch';
import {namespace} from "vuex-class";
import CreateQuizAccessForm from './CreateQuizAccessForm';
import {QuizCreateAccessForm} from "./CreateQuizAccessForm.vue";
import {mixins} from "vue-class-component";

interface QuizAccessTag extends QuizAccess {
    isMarked: boolean;
    range: [string,string]
}

const classroomShowModule = namespace('dashboard/classroom/show');

@Component({
    components: {ExamSearch,CreateQuizAccessForm}
})
export default class QuizAccessForm extends mixins(HydraMixxin) {
    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() hydraCollection: HydraCollection<QuizAccess> = null;
    @Provide() loading: boolean = false;
    @Provide() hasMarked: boolean = false;
    @Provide() showCreate:boolean = false;

    @Provide() tableProps: any = {
        rowClassName(provides:{row:any,rowIndex: number}) {
            if (provides.row.isMarked) {
                return 'marked-change';
            }
            return '';
        }
    };

    async handleDeleteAccess(access:QuizAccess) {
        const response = await service({
            url: '/_api/quiz_accesses/' + access.id,
            method: 'DELETE'
        })
        await this.load(this.hydraCollection["hydra:view"]["@id"])
    }

    get totalItems() {
        return this.hydraCollection ? this.hydraCollection["hydra:totalItems"] : 0;
    }

    get items(){
        return this.hydraCollection ? this.hydraCollection["hydra:member"] : [];
    }

    async handleSubmitCreate(result: HydraCollection<QuizAccess> ){
        this.showCreate = false;
        await this.load(this.hydraCollection["hydra:view"]["@id"])
    }

    get quizAccess() {
        return this.hydraCollection ? this.hydraCollection["hydra:member"] : [];
    }

    get count() {
        return this.hydraCollection ? this.hydraCollection["hydra:totalItems"] : 0;
    }

    async load(url: string) {
        const response = await service({
            url: url,
            method: 'GET'
        });
        const payload: HydraCollection<QuizAccess> = response.data;
        payload["hydra:member"].forEach(function (e) {
            const temp = <QuizAccessTag>e;
            temp.range = [e.openDate, e.closeDate];
            temp.isMarked = false;
        });
        this.hydraCollection = payload;
    }

    async loadData(queryInfo: any) {
        this.loading = true;
        const vueTable = buildSortQueryForVueDataTable(queryInfo);
        vueTable.addFilter(new SearchFilter("classroom", this.classroom.id + ''));
        await this.load('/_api/quiz_accesses?' + vueTable.build());
        this.loading = false;
    }
    rowChange(row: any) {
        this.hasMarked = true;
        row.isMarked = true;
    }
    async onSubmit(){
        this.loading = true;
        for (const access of <QuizAccessTag[]>this.hydraCollection["hydra:member"]){
            if(access.isMarked){
                const response = await service({
                    url: '/_api/quiz_accesses/' + access.id,
                    method: 'PUT',
                    data: {
                        'isHidden': access.isHidden,
                        'numAttempts': access.numAttempts,
                        'openDate': access.range[0],
                        'closeDate': access.range[1],
                        'quiz': hydraID(access.quiz)
                    }
                })

            }
        }

        // this.classroom = response.data;
        await this.load(this.hydraCollection["hydra:view"]["@id"])
        this.loading = false;
    }

}

</script>


<style rel="stylesheet/scss" lang="scss">
    .quiz-access-table{
        .marked-change td.el-table-column--selection{
            border-left: 5px solid lightgreen;
        }

    }
</style>