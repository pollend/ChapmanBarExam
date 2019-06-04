<template>
    <div class="section">
        <div class="container">
            <div v-for="q in questions">
                <template v-if="q.type === 'multiple_choice'">
                    {{q.content}}
                    <template>
                        <div v-for="r in q.entries"> <el-radio v-model="q.submit" value="r.id">{{ r.content }}</el-radio> </div>
                    </template>

                </template>
                <template v-else-if="q.type === 'short_answer'">

                </template>
                <el-divider></el-divider>
            </div>
            <el-button type="primary" @click="submitResults">Next Page<i class="el-icon-arrow-right el-icon-right"></i></el-button>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { getQuestions } from '@/api/quiz-session'
export default {
  name: 'Home',
  computed: {
      ...mapGetters({
         'session_id' : 'quiz-session/session_id',
          'quiz_id' : 'quiz-session/session_quiz_id'
      }),
  },
  components: { },
  data() {
    return {
        questions: null,
        responses: []
    };
  },
  created() {
      //this.$route.params.page
      this.query(this.session_id,this.quiz_id,this.$route.params.page);
  },
  watch: {
      session_id: function () {
          this.query(this.session_id,this.quiz_id,this.$route.params.page);
      }
  },
  methods: {
      query(session_id,quiz_id,page) {
          let _this = this;
          getQuestions(quiz_id,page).then((response) => {
              const {questions} = response;
              questions.forEach(function (q) {

              });
              _this.questions = questions;
          });
      },
      submitResults() {
          
      }
  },
};
</script>

<style>
    .radio-label {
        font-size: 14px;
        color: #606266;
        line-height: 40px;
        padding: 0 12px 0 30px;
    }
</style>
