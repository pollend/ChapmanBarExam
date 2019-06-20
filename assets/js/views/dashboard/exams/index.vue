<template>
    <div class="section">
        <div class="container">
            <data-tables-server :loading="loading" :data="exams" :total="count" @query-change="loadData" :pagination-props="{ pageSizes: [20, 50, 100, 200] }">
                <el-table-column sortable="true" prop="createdAt" label="Created At"></el-table-column>
                <el-table-column sortable="true" prop="updatedAt" label="Updated At"></el-table-column>
                <el-table-column sortable="true" prop="name" label="Name"></el-table-column>
<!--                <el-table-column label="Actions" min-width="100px">-->
<!--                    <template slot-scope="scope">-->
<!--                        <el-button @click="handleView(scope.row)">View</el-button>-->
<!--                        <el-button @click="handleArchive(scope.row)" type="danger">Archive</el-button>&ndash;&gt;-->
<!--                    </template>-->
<!--                </el-table-column>-->
            </data-tables-server>
        </div>
    </div>
</template>

<script lang="ts">
// import {getQuizDatatable} from "@/api/quiz";

import {Component, Provide, Vue} from "vue-property-decorator";
import service from "../../../utils/request";
import {HydraCollection} from "../../../entity/hydra";
import {Quiz} from "../../../entity/quiz";
import {buildSortQueryForVueDataTable} from "../../../utils/vue-data-table-util";
@Component
export default class ListExams extends Vue {
    @Provide() loading: boolean = false;
    @Provide() hydraCollection: HydraCollection<Quiz> = null;
    get exams() {
        return this.hydraCollection ? this.hydraCollection["hydra:member"] : [];
    }

    get count() {
        return this.hydraCollection ? this.hydraCollection["hydra:totalItems"] : 0;
    }

    async loadData(queryInfo: any) {
        this.loading = true;
        console.log(queryInfo);
        const response = await service({
            url: '/_api/quizzes?' + buildSortQueryForVueDataTable(queryInfo).build(),
            method: 'GET'
        });
        this.hydraCollection = response.data;
        this.loading = false;
    }
}
//
// export default {
//     name: 'ExamList',
//     data() {
//         return {
//             loading: false,
//             exams: null,
//             count: 0
//         }
//     },
//     methods: {
//         orderHelper(order){
//             switch (order) {
//                 case 'ascending':
//                     return  'ASC';
//                 case 'descending':
//                     return  'DESC';
//             }
//             return  '';
//         },
//         handleView(row){
//             this.$router.push({'name':'dashboard.class', 'params': {'class_id':row.id}})
//         },
//         async loadData(queryInfo) {
//             this.loading = true;
//             let sort = {};
//             if(queryInfo.sort.prop && queryInfo.sort.order)
//                 sort[queryInfo.sort.prop] = this.orderHelper(queryInfo.sort.order);
//
//             const response = await getQuizDatatable({
//                 'pageSize': queryInfo.pageSize,
//                 'sort': sort,
//                 'page': queryInfo.page - 1
//             });
//             const {quizzes} = response.data;
//             const {column_sort,payload} = quizzes;
//             const { count, page, per_page, result } = payload;
//             this.exams = result;
//             this.page = page;
//             this.count = count;
//             this.per_page = per_page;
//             this.column_sort = column_sort;
//             this.loading = false;
//         }
//     }
// }
</script>

<style rel="stylesheet/scss" lang="scss">
</style>