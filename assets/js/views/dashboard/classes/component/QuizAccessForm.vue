<template>
    <el-form ref="form" >
        <el-form-item>
            <data-tables-server class="quiz-access-table" :table-props="table_props" :pagination-show="false" layout="table" :loading="loading" :data="data" @query-change="loadAccessData">
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
                        <el-checkbox change="rowChange(scope.row)"  @change="rowChange(scope.row)"   v-model="scope.row.is_hidden"></el-checkbox>
                    </template>
                </el-table-column>
                <el-table-column prop="quiz.name" label="Exam">
                </el-table-column>
                <el-table-column prop="num_attempts" label="Attempt Count">
                    <template slot-scope="scope">
                        <el-input-number v-model="scope.row.num_attempts" @change="rowChange(scope.row)"  :min="0" ></el-input-number>
                    </template>
                </el-table-column>
            </data-tables-server>
        </el-form-item>
        <el-form-item>
            <el-button style="float:right" :disabled="!hasMarked" @click="onSubmit">Save</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import { DateTime } from 'luxon';
    export default {
        props:['classId'],
        data() {
            return {
                loading: false,
                count: 0,
                hasMarked: false,
                data: null,
                table_props: {
                    rowClassName({row, rowIndex}) {
                        if(row.isMarked){
                            return 'marked-change';
                        }
                        return '';
                    }
                }
            }
        },
        methods: {
            rowChange(row){
                this.hasMarked = true;
                row.isMarked = true;
            },
            async onSubmit(){
                this.loading = true;
                for (const access of this.data){
                    if(access.isMarked) {
                        // const response = await patchQuizAccess(this.classId, access.id, {
                        //     'is_hidden': access.is_hidden,
                        //     'num_attempts': access.num_attempts,
                        //     'open_date': access.range[0],
                        //     'close_date':  access.range[1],
                        //     'quiz': access.quiz.id
                        // });
                    }
                }
                await this.loadAccessData();
                this.loading = false;
            },
            async loadAccessData() {
                this.loading = true;
                const response = await getQuizAccess(this.classId);
                const {quiz_access} = response.data;
                quiz_access.forEach(function (e) {
                    e.range = [e.open_date, e.close_date];
                    e.isMarked = false;
                });
                this.data = quiz_access;
                this.loading = false;
            }
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