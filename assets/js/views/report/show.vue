<template>
    <div v-loading="questions === null" class="overview-container">
        <div class="columns is-mobile">
            <div class="column is-5 is-pulled-left">
                <p>
                    Course #:
                </p>
                <p>
                    Course Title:
                </p>
                <p>
                    Day\Time:
                </p>
            </div>
            <div class="column is-5 is-pulled-left">
                <p>
                    Instructor:
                </p>
                <p>
                    Description:
                </p>
                <p>
                    Term/Year:
                </p>
            </div>
        </div>


        <div class="columns">
            <div class="column is-one-fifth">
                Response Description:
            </div>
            <div class="column  is-size-7">
                <div class="columns">
                    <div class="column is-4">
                        <p>&lt - &gt correct</p>
                        <p>&lt A-Z &gt student's incorrect response</p>

                    </div>
                    <div class="column is-4">
                        <p>&lt # &gt multiple marks</p>
                        <p>&lt * &gt bonus test item</p>

                    </div>
                    <div class="column is-4">
                        <p>&lt _ &gt no response</p>
                    </div>
                </div>
            </div>
        </div>

        <table v-for="(group_entry, group_index) in chunkedRespones"  class="table is-bordered is-size-7 is-full-width overview-table" >
            <thead>
                <tr>
                    <th>
                        Test Items:
                    </th>
                    <th v-for="(item, index) in group_entry">
                        {{((index*5) + 1) + group_index * 5 * 10}}-{{((index*5) + 5) + group_index * 5 * 10}}
                    </th>
                </tr>
            </thead>
            <tbody>
                <td>
                    Answers:
                </td>
                <td v-for="(item, index) in group_entry" class="response-entry">
                    <template v-for="q in item" >
                        <router-link :to="`#question-${q.id}`"><el-link>{{ responseCharacter(q) }}</el-link></router-link>,
                    </template>
                </td>
            </tbody>
        </table>

        <div v-for="(q,index) in questions" v-bind:key="q.id" v-bind:id="`question-${q.id}`">
            <div class="tag-container">
                <!-- el-tag elements clogging up event queue when unbinding replaced with span for performance -->
                <span v-for="tag in q.tags" :key="tag.id" class="el-tag el-tag--info el-tag--medium el-tag--light">{{tag.name}}</span>
<!--                <el-tag :disableTransitions="true" type="info" v-for="tag in q.tags" :key="tag.id">{{tag.name}}</el-tag>-->
            </div>
            <template v-if="q.type === 'multiple_choice'">
                <p class="question_statement"  v-bind:class="{'question-selected': question_mark ===  `question-${q.id}` }">
                    {{index + 1}}. {{q.content}}
                </p>
                <template>
                    <div v-for="(e,index) in orderEntries(q.entries)"  :key="e.id" >
                        ({{mapCharacterIndex(index)}}) <multiple-choice-entry :correct_entry="q.correct_entry" :entry="e" :response="(q.id in responses) ? responses[q.id].choice : null "></multiple-choice-entry>
                    </div>
                </template>
            </template>
            <template v-else-if="q.type === 'text_block'">
                <el-card class="box-card">
                    {{index + 1}}. {{q.content}}
                </el-card>
            </template>
            <template v-else-if="q.type === 'short_answer'">

            </template>
            <el-divider></el-divider>
        </div>
    </div>
</template>

<script>
import {getReport} from "@/api/report";
import NProgress from 'nprogress';
import MultipleChoiceEntry from './component/MultipleChoiceEntry'
import  _ from 'lodash';
export default {
  name: 'ReportShow',
  components: {MultipleChoiceEntry },
  data() {
    return {
        questions: null,
        responses: null,
        started_loading: false,
        question_mark: ''
    };
  },
  watch: {
    '$route' (to, from) {
        this.question_mark =  to.hash.split('#')[1]
    }
  },
  async created(){
      let _this = this;
      NProgress.start();
      let response = await getReport(this.$route.params.report_id);
      const {questions,responses} = response.data;
      _this.questions = questions;
      _this.responses = responses;
      NProgress.done();
  },
  computed: {
      chunkedRespones() {
          return _.chunk(_.chunk(_.filter(this.questions,function (q) {
              return q.type === 'multiple_choice';
          }),5),10)
      }
  },
  methods: {
      responseCharacter(question) {
          let choice = (question.id in this.responses) ? this.responses[question.id].choice : null;
          if(choice === null)
              return '_';
          if(question.correct_entry.id === choice.id){
              return '-';
          }
          for(const [index,e] of this.orderEntries(question.entries).entries()){
              if(e.id === choice.id){
                  return this.mapCharacterIndex(index);
              }
          }
          return '?';
      },
      orderEntries(entries){
          return _.orderBy(entries,function (o) {
              return o.order;
          })
      },
      mapCharacterIndex(index){
          const lookup = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
          return lookup[index];
      }
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">

    .overview-container {
        .response-description {
            margin-bottom: 2rem;
        }

        .question_statement {
            margin-bottom: 1rem;
        }

        .question-selected{
            color: #090048;
        }

        .tag-container{
            margin-bottom: .5rem;
            .el-tag{
                margin-right: .2rem;
            }
        }
        .overview-table {
            width: 100%;

            td.response-entry, th.response-entry {
                text-align: center;
            }
        }

        min-height: 40rem;
    }

</style>
