<template>
    <div class="section">
        <div class="container">
            <el-table :data="reports">
                <el-table-column
                        prop="createdAt"
                        label="Created At">
                </el-table-column>
                <el-table-column
                        prop="submittedAt"
                        label="Submitted At">
                </el-table-column>
                <el-table-column
                        prop="quiz.name"
                        label="Name">
                </el-table-column>
                <el-table-column label="View">
                    <template slot-scope="scope">
                        <el-button
                                size="mini"
                                @click="handleView(scope.row)">view</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import {namespace} from "vuex-class";
import User from "../../entity/user";
import service from "../../utils/request";
import {ExistFilter, FilterBuilder} from "../../api/filters/filter";

const authModule = namespace('auth')

@Component
export default class ReportList extends Vue {
    @authModule.Getter("user") user: User;
    @Provide() reports:any = null;

    handleView(row: any){
        this.$router.push({'name':'app.report.show','params':{'report_id': row.id}})
    }

    created() {
        service({
            url: '/_api/quiz_sessions?' + (new FilterBuilder()).addFilter(new ExistFilter('submittedAt',true)).build(),
            method: 'GET'
        }).then((response) => {
            const collection = response.data;

        }).catch((error) => {

        });
    }
}
//
// export default {
//   name: 'ReportIndex',
//   components: { },
//   computed: {
//       ...mapGetters({
//           'userId': 'user/id',
//       }),
//   },
//   data() {
//     return {
//         reports: null
//     };
//   },
//   async created() {
//       const response = await getReports(this.userId);
//       const {reports} = response.data;
//       this.reports = reports;
//   },
//   methods: {
//       handleView(row){
//           this.$router.push({'name':'app.report.show','params':{'report_id': row.id}})
//       }
//   },
// };
</script>

<style>
</style>
