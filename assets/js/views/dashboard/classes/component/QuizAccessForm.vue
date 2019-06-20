<template>
    <div>
        <el-form ref="form" v-if="classroom">
            <el-form-item>
                <data-tables-server class="quiz-access-table" :table-props="tableProps" :loading="loading" :data="data" @query-change="loadData">
                    <el-table-column type="selection" width="55">
                    </el-table-column>
                    <el-table-column prop="open_date" label="Open And Close" width="460">
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
                    <el-table-column prop="is_hidden" label="Hide" width="70">
                        <template slot-scope="scope">
                            <el-checkbox change="rowChange(scope.row)"  @change="rowChange(scope.row)"   v-model="scope.row.isHidden"></el-checkbox>
                        </template>
                    </el-table-column>
                    <el-table-column prop="quiz.name" label="Exam">
                    </el-table-column>
                    <el-table-column prop="num_attempts" label="Attempt Count">
                        <template slot-scope="scope">
                            <el-input-number v-model="scope.row.numAttempts" @change="rowChange(scope.row)"  :min="0" ></el-input-number>
                        </template>
                    </el-table-column>
                </data-tables-server>
            </el-form-item>
            <el-form-item>
                <el-button style="float:right; margin-left: .6rem;"  @click="() => {createNewEntry = !createNewEntry}">Create</el-button>
                <el-button style="float:right" :disabled="!hasMarked" @click="onSubmit">Save</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts">

import Classroom from "../../../../entity/classroom";
import {Component, Prop, Provide, Vue} from "vue-property-decorator";
import {HydraCollection} from "../../../../entity/hydra";
import QuizAccess from "../../../../entity/quiz-access";
import service from "../../../../utils/request";
import {buildSortQueryForVueDataTable} from "../../../../utils/vue-data-table-util";
import {SearchFilter} from "../../../../utils/filter";


interface QuizAccessTag extends QuizAccess {
    isMarked: boolean;
    range: [string,string]
}

@Component
export default class QuizAccessForm extends Vue {
    @Prop() readonly classroom: Classroom = null;
    @Provide() hydraCollection: HydraCollection<QuizAccess> = null;
    @Provide() data: QuizAccessTag[] = [];
    @Provide() loading: boolean = false;
    @Provide() hasMarked: boolean = false;
    @Provide() tableProps: any = {
        rowClassName(provies:{row:any,rowIndex: number}) {
            if (provies.row.isMarked) {
                return 'marked-change';
            }
            return '';
        }
    };
    @Provide() createNewEntry: boolean = false;

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
        this.hydraCollection = response.data;

        const quizAccessTag: QuizAccessTag[] = <QuizAccessTag[]>this.hydraCollection["hydra:member"]

        quizAccessTag.forEach(function (e) {
            e.range = [e.openDate, e.closeDate];
            e.isMarked = false;
        });
        this.data = quizAccessTag;
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
        for (const access of this.data){
            if(access.isMarked){
                const response = await service({
                    url: '/_api/quiz_accesses/' + access.id,
                    method: 'PUT',
                    data: {
                        'isHidden': access.isHidden,
                        'numAttempts': access.numAttempts,
                        'openDate': access.range[0],
                        'closeDate': access.range[1]
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