<template>
    <div class="section">
        <div class="container">
            <el-table :data="reports">
                <el-table-column
                        prop="created_at"
                        label="Created At">
                </el-table-column>
                <el-table-column
                        prop="submitted_at"
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

<script>
import {getReports} from "@/api/report";
import {mapGetters} from "vuex";

export default {
  name: 'ReportIndex',
  components: { },
  computed: {
    ...mapGetters([
        'userId',
    ]),
  },
  data() {
    return {
        reports: null
    };
  },
  created() {
      this.query();
  },
  methods: {
      query() {
          getReports(this.userId).then((response) => {
              const {reports} = response.data;
              this.reports = reports;
          });
      },
      handleView(row){
          this.$router.push({'name':'app.report.show','params':{'report_id': row.id}})
      }
  },
};
</script>

<style>
</style>
