<template>
    <div class="section">
        <div class="container">
            <!--            <el-button>Create Exam</el-button>-->
            <data-tables-server :loading="loading" :data="exams" :total="count" @query-change="loadData" :pagination-props="{ pageSizes: [20, 50, 100, 200] }">
                <el-table-column sortable="true" prop="createdAt" label="Created At"></el-table-column>
                <el-table-column sortable="true" prop="updatedAt" label="Updated At"></el-table-column>
                <el-table-column sortable="true" prop="email" label="Email"></el-table-column>
                <el-table-column label="Actions" min-width="100px">
                    <template slot-scope="scope">
                        <!--                        <el-button @click="handleView(scope.row)">View</el-button>-->
                    </template>
                </el-table-column>
            </data-tables-server>
        </div>
    </div>
</template>

<script lang="ts">

    import {Component, Provide, Vue} from "vue-property-decorator";
    import service from "../../../utils/request";
    import {HydraCollection} from "../../../entity/hydra";
    import {Quiz} from "../../../entity/quiz";
    import {buildSortQueryForVueDataTable} from "../../../utils/vue-data-table-util";
    import User from "../../../entity/user";

    @Component
    export default class ListUsers extends Vue {
        @Provide() loading: boolean = false;
        @Provide() hydraCollection: HydraCollection<User> = null;

        get exams() {
            return this.hydraCollection ? this.hydraCollection["hydra:member"] : [];
        }

        get count() {
            return this.hydraCollection ? this.hydraCollection["hydra:totalItems"] : 0;
        }

        handleView(row: Quiz){
            this.$router.push({'name':'dashboard.exam.show','params':{'quiz_id' : row.id+''}})
        }


        async loadData(queryInfo: any) {
            this.loading = true;
            const response = await service({
                url: '/_api/users?' + buildSortQueryForVueDataTable(queryInfo).build(),
                method: 'GET'
            });
            this.hydraCollection = response.data;
            this.loading = false;
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
</style>
