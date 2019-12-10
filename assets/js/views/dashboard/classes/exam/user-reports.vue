<template>
    <el-table
        :data="users"
        style="width: 100%">
        <el-table-column type="expand">
            <template slot-scope="props">
                <p>State: {{ props.row.state }}</p>
                <p>City: {{ props.row.city }}</p>
                <p>Address: {{ props.row.address }}</p>
                <p>Zip: {{ props.row.zip }}</p>
                <UserReportQuizSession :user="props.row" :report_id="$route.params.report_id" :class_id="$route.params.class_id"></UserReportQuizSession>
            </template>
        </el-table-column>
        <el-table-column
            label="email"
            prop="email">
        </el-table-column>
    </el-table>
</template>

<script lang="ts">

import {Component, Provide, Vue} from "vue-property-decorator";
import {mixins} from "vue-class-component";
import {HydraCollection, HydraMixxin} from "../../../../entity/hydra";
import service from "../../../../utils/request";
import {ExistFilter, FilterBuilder, SearchFilter} from "../../../../utils/filter";
import User from "../../../../entity/user";
import UserReportQuizSession from "./component/UserReportQuizSession.vue";
@Component({
    components: {UserReportQuizSession}
})
export default class UserReports  extends mixins(HydraMixxin){
    @Provide() collection: HydraCollection<User> = null;
    @Provide() users: User[] = null
    @Provide() loading: boolean = false;


    async _loadUsers() {
        const builder = new FilterBuilder();
        builder.addFilter(new SearchFilter("classes",this.$route.params.class_id));
        const response = await service({
            url: '/_api/users?' + builder.build(),
            method: 'GET'
        });
        this.collection = response.data
        this.users = this.collection["hydra:member"]
    }

    async created() {
        await this._loadUsers()
    }

}

</script>
