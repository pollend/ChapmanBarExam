<template>
    <div class="section">
        <div class="container">
            <div v-for="(q,index) in questions" v-bind:key="q.id">
                <template v-if="q.type === 'multiple_choice'">
                    <p class="question_statement">
                        {{index}}. {{q.content}}
                    </p>
                    <template>
                        <el-radio-group v-model="q.value">
                            <div v-for="e in q.entries"  :key="e.id" >
                                <el-radio :label="e.id">{{ e.content }}</el-radio>
                            </div>
                        </el-radio-group>
                    </template>

                </template>
                <template v-else-if="q.type === 'text_block'">
                    <p>
                        {{q.content}}
                    </p>
                </template>
                <template v-else-if="q.type === 'short_answer'">

                </template>
                <el-divider></el-divider>
            </div>
            <el-button type="primary" @click.native.prevent="submitResults">Next Page<i class="el-icon-arrow-right el-icon-right"></i></el-button>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import {getQuestions, getResponses, postResponse} from '@/api/quiz-session'
import NProgress from 'nprogress';
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
        questions: null
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
              const {questions} = response.data;
              _this.questions = questions;
              getResponses(session_id,page).then((response)=>{
                  const {responses} = response.data;
                  console.log(responses);
                  let value_map = {};
                  responses.forEach(function (resp) {
                      if(resp.type === 'multiple_choice') {
                          value_map[resp.question.id] = resp.choice.id;
                      }
                  });
                  _this.questions.forEach(function (q) {
                      if(q.id in value_map)
                        q.value = value_map[q.id];
                  });
                  _this.$forceUpdate();
              });
          });
      },
      getValues(){
          let target = {};
          this.questions.forEach(function (q) {
              if(q.value) {
                  target[q.id] = q.value;
              }
          });
          return target;
      },
      submitResults() {
          let result = this.getValues();
          postResponse(this.session_id,this.$route.params.page,{'responses':result}).then((response) => {
              this.$router.go();
          });
      }
  },
};
</script>

<style>
    .question_statement {
        margin-bottom: 1rem;
    }
</style>
