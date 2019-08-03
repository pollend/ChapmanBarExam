<template>

    <div class="section">
        <div class="container">
            <div v-if="quiz">
                <el-card v-for="(questionGroups,group_index) in questionGroups(quiz)" >
                    <template v-for="(question,index) in orderQuestions(questionGroups)">
                       <div class="question-container">
                           <div class="question-number">{{index}}.</div>
                           <div class="question-form">
                                <template v-if="hydraType(question) === 'MultipleChoiceQuestion'">
                                    <multiple-choice-form :question="question"></multiple-choice-form>
                                </template>
                                <template v-else-if="hydraType(question) === 'TextBlockQuestion'">

                                </template>
                           </div>
                       </div>
                    </template>
                </el-card>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";
    import service from "../../../utils/request";
    import {Quiz} from "../../../entity/quiz";
    import {mixins} from "vue-class-component";
    import {HydraMixxin} from "../../../entity/hydra";
    import _ from "lodash";
    import {QuizQuestion} from "../../../entity/quiz-question";
    import MultipleChoiceForm from "./component/multi-choice-form.vue";

    import NProgress from 'nprogress';

    @Component({components: {MultipleChoiceForm}})
    export default class ShowExam extends mixins(HydraMixxin){
        @Provide() quiz: Quiz = null;
        @Provide() isLoading: boolean = false;

        questionGroups(quiz : Quiz){
            return _.groupBy(quiz.questions,(q) => {
                return q.group;
            })
        }

        orderQuestions(questions: QuizQuestion[]) {
            return _.orderBy(questions,(q) => {
                return q.order;
            })
        }

        async created(){
            NProgress.start();
            this.isLoading = true;
            const response = await service({
                url: '/_api/quizzes/' + this.$router.currentRoute.params['quiz_id'],
                method: 'GET'
            });
            this.quiz = response.data;
            this.isLoading = true;
            NProgress.done()

        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
    .question-container {
        position: relative;
        .question-number {
            position: absolute;
        }

        .question-form {
            margin-left: 40px;
        }
    }

</style>