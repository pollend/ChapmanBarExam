<template>
    <div class="section">
        <div class="container">
            <data-tables-server :loading="loading" :data="classes" :total="count" @query-change="loadData" :pagination-props="{ pageSizes: [10, 50, 100, 200] }">
                <el-table-column sortable="true" prop="createdAt" label="Created At"></el-table-column>
                <el-table-column sortable="true" prop="updatedAt" label="Updated At"></el-table-column>
                <el-table-column sortable="true" prop="name" label="Name"></el-table-column>
                <!-- TODO: Add sorting for number of students -->
                <el-table-column prop="numberOfStudents" label="Number Of Students"></el-table-column>
                <el-table-column label="Actions" min-width="100px">
                    <template slot-scope="scope">
                        <el-button @click="handleView(scope.row)">View</el-button>
                    </template>
                </el-table-column>
            </data-tables-server>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Provide, Vue} from "vue-property-decorator";
import service from "../../../utils/request";
import Classroom from "../../../entity/classroom";
import {HydraCollection} from "../../../entity/hydra";
import {buildSortQueryForVueDataTable} from "../../../utils/vue-data-table-util";

@Component
export default class ClassLists extends Vue {
    @Provide() loading: boolean = false;
    @Provide() columnSort: [] = [];
    @Provide() hydraCollection: HydraCollection<Classroom> = null;

    get classes() {
        return this.hydraCollection ? this.hydraCollection["hydra:member"] : [];
    }

    get count() {
        return this.hydraCollection ? this.hydraCollection["hydra:totalItems"] : 0;
    }

    handleView(row: Classroom){
        this.$router.push({'name':'dashboard.class','params':{'class_id' : row.id+''}})
    }

    async loadData(query: {}){
        this.loading = true;
        const response = await service({
            url: '/_api/classrooms?' + buildSortQueryForVueDataTable(query).build(),
            method: 'GET'
        });
        this.hydraCollection = response.data;
        this.loading = false;
    }
}
</script>

<style rel="stylesheet/scss" lang="scss">
</style>