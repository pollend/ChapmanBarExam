<template>
    <div class="section">
        <div class="container">
            <data-tables-server :loading="loading" :data="exams" :total="count" @query-change="loadData" :pagination-props="{ pageSizes: [10, 50, 100, 200] }">
                <el-table-column sortable="true" prop="created_at" label="Created At"></el-table-column>
                <el-table-column sortable="true" prop="updated_at" label="Updated At"></el-table-column>
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
@Component
export default class ListExams extends Vue {
    @Provide() loading: boolean = false;
    @Provide() exams: any = [];

    async loadData(queryInfo: any){
        const response = await service({
            url:'/_api/quizzes',
            method: 'GET'
        });
        const exams : HydraCollection<Quiz>= response.data;

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