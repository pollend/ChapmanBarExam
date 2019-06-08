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
export default {
  name: 'ExamStart',
  components: { },
  data() {
    return {};
  },
  created() {
  },
  methods: {
      handleQuizStart() {
        postClassroomQuizStart(this.$route.params.class_id,this.$route.params.quiz_id)
        .then((response) => {
            this.$router.go();
        });
    },
  },
};
</script>

<style>
</style>
