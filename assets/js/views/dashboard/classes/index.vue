<template>
    <div class="section">
        <div class="container">
            <data-tables-server :loading="loading" :data="classes" :total="count" @query-change="loadData" :pagination-props="{ pageSizes: [10, 50, 100, 200] }">
                <el-table-column sortable="true" prop="created_at" label="Created At"></el-table-column>
                <el-table-column sortable="true" prop="updated_at" label="Updated At"></el-table-column>
                <el-table-column sortable="true" prop="name" label="Name"></el-table-column>
                <!-- TODO: Add sorting for number of students -->
                <el-table-column prop="number_of_students" label="Number Of Students"></el-table-column>
                <el-table-column label="Actions" min-width="100px">
                    <template slot-scope="scope">
                        <el-button @click="handleView(scope.row)">View</el-button>
                        <el-button @click="handleArchive(scope.row)" type="danger">Archive</el-button>
                    </template>
                </el-table-column>
            </data-tables-server>
        </div>
    </div>
</template>

<script lang="ts">
// import {getReports} from "@/api/report";
// import {mapGetters} from "vuex";
// import {getClassesDatatable} from "@/api/classes";
// import MixinDatatable from "@/mixxins/data-table"
// import {Component, Provide, Vue} from "vue-property-decorator";
// // import from 'VueD'
//
// @Component
// export default class ClassList extends Vue{
//     @Provide() columnSort: [] = [];
//     @Provide() classes: [] = [];
//     @Provide() count: [] = [];
//     @Provide() columnSort: [] = [];
//
//
//
//     async loadData(query: {}){
//           this.loading = true;
//           let sort = {};
//           if(queryInfo.sort.prop && queryInfo.sort.order)
//               sort[queryInfo.sort.prop] = this.order(queryInfo.sort.order);
//
//           const response = await getClassesDatatable({'pageSize':queryInfo.pageSize,'sort' : sort,'page': queryInfo.page - 1});
//           const {classes} = response.data;
//           const {column_sort,payload} = classes;
//           const { count, page, per_page, result } = payload;
//           this.classes = result;
//           this.page = page;
//           this.count = count;
//           this.per_page = per_page;
//           this.column_sort = column_sort;
//           this.loading = false;
//     }
//
// }
// export default {
//   name: 'ClassesList',
//   components: { },
//   computed: {
//       currentSort() {
//           let props = [];
//           console.log(this.column_sort);
//           for (const [key,value] of Object.entries(this.column_sort)){
//               result.push({'prop': key,'order': value})
//           }
//           return result;
//       }
//   },
//   data() {
//     return {
//         column_sort: {},
//         classes: null,
//         count: 0,
//         per_page: 0,
//         page: 0,
//         loading:false
//     };
//   },
//   mixins:[MixinDatatable],
//   methods: {
//       handleView(row){
//           this.$router.push({'name':'dashboard.class', 'params': {'class_id':row.id}})
//       },
//       handleArchive(row){
//       },
//       async loadData(queryInfo){
//           this.loading = true;
//           let sort = {};
//           if(queryInfo.sort.prop && queryInfo.sort.order)
//               sort[queryInfo.sort.prop] = this.order(queryInfo.sort.order);
//
//           const response = await getClassesDatatable({'pageSize':queryInfo.pageSize,'sort' : sort,'page': queryInfo.page - 1});
//           const {classes} = response.data;
//           const {column_sort,payload} = classes;
//           const { count, page, per_page, result } = payload;
//           this.classes = result;
//           this.page = page;
//           this.count = count;
//           this.per_page = per_page;
//           this.column_sort = column_sort;
//           this.loading = false;
//       },
//   },
// };
</script>

<style rel="stylesheet/scss" lang="scss">
</style>