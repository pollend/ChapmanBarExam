<template>
    <data-tables-server :loading="loading" :data="users" :total="count" @query-change="loadCollection" :pagination-props="{ pageSizes: [10, 50, 100, 200] }">
        <el-table-column sortable="true" prop="email" label="Email"></el-table-column>
        <!-- TODO: Add sorting for number of students -->
        <el-table-column prop="lastLogin" label="Last Logged In"></el-table-column>
        <el-table-column label="Actions" min-width="100px">
            <template slot-scope="scope">
                <el-button @click="removeUserFromClass(scope.row)">Remove</el-button>
                <!--                        <el-button @click="handleArchive(scope.row)" type="danger">Archive</el-button>-->
            </template>
        </el-table-column>
    </data-tables-server>
</template>

<script lang="ts">

import {Component, Provide, Vue} from "vue-property-decorator";
import service from "../../../utils/request";
import {namespace} from "vuex-class";
import Classroom from "../../../entity/classroom";
import {FilterBuilder, SearchFilter} from "../../../utils/filter";
import {HydraCollection, HydraMixxin} from "../../../entity/hydra";
import {mixins} from "vue-class-component";
import User from "../../../entity/user";
import {buildSortQueryForVueDataTable} from "../../../utils/vue-data-table-util";

const classroomShowModule = namespace('dashboard/classroom/show');

@Component
export default class ClassesUser  extends mixins(HydraMixxin){

    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() collection: HydraCollection<User> = null;
    @Provide() loading: boolean = false;
    @Provide() query: {} = {};

    get users() {
        return this.collection ? this.collection["hydra:member"] : [];
    }

    get count() {
        return this.collection ? this.collection["hydra:totalItems"] : 0;
    }

    async loadCollection(query: {}) {
        this.query = query;
        const filter = buildSortQueryForVueDataTable(query);
        filter.addFilter(new SearchFilter("classes",this.hydraID(this.classroom)));
        this.loading = true;
        const response = await service({
            url: '/_api/users?' + filter.build(),
            method: 'GET'
        });
        this.collection = response.data;
        this.loading = false;
    }

    async removeUserFromClass(user: User){
        this.loading = true;
        const result = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/user/' + user.id,
            method: 'DELETE'
        });
        this.$notify({
            title: 'Classroom',
            message: `User Removed: ${user.email}`
        });
        await this.loadCollection(this.query);


        this.loading = false;
    }

}

</script>
