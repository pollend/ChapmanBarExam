<template>
    <div>
        <h2>History</h2>
        <data-tables-server layout="table"  :table-props="tableProps" :data="sessions" @query-change="loadCollection">
            <el-table-column label="Score" prop="score"  min-width="100px">
                <template slot-scope="scope">
                    {{scope.row.score}} / {{scope.row.quiz.max_score}} ({{scope.row.score/scope.row.quiz.max_score}}%)
                </template>
            </el-table-column>
            <el-table-column sortable="true" prop="submittedAt" label="Submitted At"></el-table-column>
            <el-table-column label="Actions" min-width="100px">
                <template slot-scope="scope">
                    <el-button v-if="current_session_id !== (scope.row.id+ '')" @click="handleView(scope.row)">Select</el-button>
                </template>
            </el-table-column>
        </data-tables-server>
        <el-divider/>

        <el-tabs v-model="tab" type="border-card" @tab-click="handleTab">
            <el-tab-pane label="Standard" name="dashboard.class.report.students.standard"></el-tab-pane>
            <el-tab-pane label="Overview" name="dashboard.class.report.students.breakdown"></el-tab-pane>
            <template>
                <router-view/>
            </template>
        </el-tabs>
    </div>
</template>

<script lang="ts">
    import {Component, Provide} from "vue-property-decorator";
    import {mixins} from "vue-class-component";
    import {HydraCollection, HydraMixxin} from "../../../../../entity/hydra";
    import QuizSession from "../../../../../entity/quiz-session";
    import {buildSortQueryForVueDataTable} from "../../../../../utils/vue-data-table-util";
    import {ExistFilter, SearchFilter} from "../../../../../utils/filter";
    import service from "../../../../../utils/request";

    @Component
    export default class Container  extends mixins(HydraMixxin) {
        @Provide() collection: HydraCollection<QuizSession> = null;
        @Provide() tab: string = '';
        get sessions() : QuizSession[] {
            return this.collection ? this.collection["hydra:member"] : []
        }

        get current_session_id() {
            return this.$route.params["session_id"]
        }
        //
        handleTab(tab: any, event: string) {
            this.$router.push({
                name: tab.name,
                params: this.$route.params
            })
        }

        @Provide() tableProps: any = {
            rowClassName(provides:{row:any,rowIndex: number}) {
                return provides.row.match ? "session-selected": "";
            }
        };

        async created(){
            this.tab = this.$router.currentRoute.name;
        }


        handleView(row: QuizSession) {
            this.$router.push({
                'name': this.$route.name,
                'params': {
                    'class_id':  this.$route.params["class_id"] + '',
                    'report_id': this.$route.params["report_id"] + '',
                    'user_id': this.$route.params["user_id"] + '',
                    'session_id': row.id + ''
                }
            })
            this.updateSelected()
        }

        updateSelected() {
            for(var e in this.collection["hydra:member"]){
                const entry :any = this.collection["hydra:member"][e];
                entry["match"] = (this.collection["hydra:member"][e].id + "" == this.$route.params["session_id"])
            }
        }

        async loadCollection(query: {}) {
            const filter = buildSortQueryForVueDataTable(query);
            filter.addFilter(new ExistFilter('submittedAt', true))
            filter.addFilter(new SearchFilter("owner", this.$route.params["user_id"]  + ''))
            filter.addFilter(new SearchFilter("quiz",this.$route.params["report_id"] + ''))
            filter.addFilter(new SearchFilter("classroom",this.$route.params["class_id"] + ''))
            const response = await service({
                url: '/_api/quiz_sessions?' + filter.build(),
                method: 'GET'
            })
            this.collection = response.data
            this.updateSelected()
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
   .el-table__row.session-selected {
       background: #effff0;

   }
</style>
