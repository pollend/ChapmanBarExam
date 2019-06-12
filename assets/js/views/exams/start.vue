<template>
    <div class="section">
        <div class="container">
            This is some information about startting quiz
            <el-divider></el-divider>
            <el-button  @click="handleQuizStart"  type="primary" style="width: 100%">Begin</el-button>
        </div>
    </div>
</template>

<script>
import { postClassroomQuizStart } from '@/api/classes';
import {mapGetters} from "vuex";
export default {
  name: 'ExamStart',
  components: { },
  computed: {
      ...mapGetters({
          'userId': 'user/id',
      }),
  },
  data() {
    return {};
  },
  created() {
  },
  methods: {
      handleQuizStart() {
        postClassroomQuizStart(this.$route.params.quiz_access_id,this.userId)
        .then((response) => {
            this.$router.go();
        });
    },
  },
};
</script>

<style>
</style>
