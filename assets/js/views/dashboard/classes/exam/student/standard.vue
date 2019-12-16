<template>
    <div v-if="questions !== null" class="overview-container">
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
        <QuestionTicks :questions="questions" :responses="responses"></QuestionTicks>
        <QuestionResponseResult :questions="questions" :responses="responses"></QuestionResponseResult>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Watch} from "vue-property-decorator";
import {mixins} from "vue-class-component";
    import {HydraCollection, HydraMixxin} from "../../../../../entity/hydra";
    import {QuizQuestion} from "../../../../../entity/quiz-question";
    import {MultipleChoiceResponse} from "../../../../../entity/quiz-response";
    import NProgress from "nprogress";
    import service from "../../../../../utils/request";
    import _ from "lodash";

    import QuestionResponseResult from "../../../../../components/Exam/QuestionResponseResult";
    import QuestionTicks from "../../../../../components/Exam/QuestionTicks";

@Component({
    components: {QuestionResponseResult,QuestionTicks},
})
export default class StandardReport  extends mixins(HydraMixxin) {
    @Provide() questions: HydraCollection<QuizQuestion> = null;
    @Provide() responses: HydraCollection<MultipleChoiceResponse> = null;

    @Watch('$route.params.session_id')
    async onSessionChange(value: string, oldValue: string) {
        this.questions = null
        await this.updateSession()
    }

    async updateSession(){
        NProgress.start();
        await Promise.all([
            service({
                url: '/_api/quiz_responses/session/' +  this.$route.params['session_id'],
                method: 'GET'
            }).then((response) => {
                this.responses = response.data;
            }).catch((err) => {

            }),
            service({
                url: '/_api/questions/sessions/' +  this.$route.params['session_id'],
                method: 'GET'
            }).then((response) => {
                this.questions = response.data;
                this.questions['hydra:member'] = _.orderBy(this.questions['hydra:member'], ['order'])
            }).catch((err) => {

            })]);
        NProgress.done();
    }

    async created() {
       await this.updateSession()
    }
}
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

        min-height: 40rem;
    }

</style>

