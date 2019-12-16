<template>

    <div class="section">
        <div class="container">
            <div v-if="quiz">
                <el-card v-for="(questionGroups,group_index) in questionGroups(quiz)" >
                    <template v-for="(question,index) in orderQuestions(questionGroups)">
                        <QuestionEntry :index="index"  :question="question"/>
<!--                        <MultipleChoiceForm :question="question"></MultipleChoiceForm>-->
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
    import QuestionEntry from "./component/QuestionEntry"

    import NProgress from 'nprogress';

    @Component({components: {QuestionEntry,MultipleChoiceForm}})
    export default class ShowExam extends mixins(HydraMixxin){
        @Provide() quiz: Quiz = null;
        @Provide() isLoading: boolean = false;
        @Provide() editEntry: number = 0

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
