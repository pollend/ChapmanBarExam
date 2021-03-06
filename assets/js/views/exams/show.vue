<template>
    <div class="section">
        <div class="container" v-if="questions">
            <div v-for="(q,index) in questions['hydra:member']" v-bind:key="q.id">
                <template v-if="q['@type'] === 'MultipleChoiceQuestion'">
                    <p class="question_statement">
                        {{(index + 1)}}. {{q.content}}
                    </p>
                    <template>
                        <el-radio-group v-model="q.value" @change="handleQuestionChange(q)">
                            <div v-for="(e,index) in orderEntries(q.entries)"  :key="e.id" >
                                <el-radio class="multiple-choice-entry" :label="e.id">({{mapCharacterIndex(index)}}) {{ e.content }}</el-radio>
                            </div>
                        </el-radio-group>
                    </template>
                </template>
                <template v-else-if="q['@type'] === 'TextBlockQuestion'">
                    <p class="question_statement">
                        {{(index + 1)}}. {{q.content}}
                    </p>
                    <p>
                        {{q.content}}
                    </p>
                </template>
                <template v-else-if="q['@type']=== 'short_answer'">

                </template>
                <el-divider></el-divider>
            </div>
            <el-button type="primary" @click.native.prevent="submitResults">Next Page<i class="el-icon-arrow-right el-icon-right"></i></el-button>
        </div>
    </div>
</template>

<script lang="ts">
import NProgress from 'nprogress';
import {Component, Provide, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";
import User from "../../entity/user";
import QuizSession from "../../entity/quiz-session";
import service from "../../utils/request";
import {HydraCollection, hydraID, HydraMixxin} from "../../entity/hydra";
import {MultipleChoiceEntry, MultipleChoiceQuestion, TextBlockQuestion} from "../../entity/quiz-question";
import _ from 'lodash';
import {mixins} from "vue-class-component";
import {ValidateMix} from "../../mixxins/validate-mix";
import {MultipleChoiceResponse, QuizQuestionResponse} from "../../entity/quiz-response";

const authModule = namespace('auth');
const quizSessionModel = namespace('app/user-quiz-session');

@Component
export default class ShowQuizPage extends mixins(ValidateMix,HydraMixxin) {
    @authModule.Getter("user") user: User;
    @quizSessionModel.Getter("session") session: QuizSession;
    @quizSessionModel.Action("submit") submit: ({}) => Promise<QuizSession>;
    @quizSessionModel.Action("save") save: ({}) => Promise<QuizSession>;
    @Provide() questions: HydraCollection<MultipleChoiceQuestion | TextBlockQuestion> = null;

    async created() {
        await this.update()
    }

    async  update () {
        NProgress.start();
        let result: HydraCollection<MultipleChoiceQuestion | TextBlockQuestion> = null;
        try {
            let response = await service({
                url: '/_api/questions/sessions/' + this.session.id + '/page/' + this.$router.currentRoute.params['page'],
                method: 'GET'
            });
            result = response.data;
        } catch (err) {
            this.hydraErrorWithNotify(err);
            NProgress.done();
            return
        }

        for(let response of this.session.responses){
            if(response["@type"] == 'MultipleChoiceResponse'){
                let resp: MultipleChoiceResponse = response as MultipleChoiceResponse;
                let question = result["hydra:member"].find((q) => {
                    return this.checkHydraMatch(q,resp.question)
                }) as MultipleChoiceQuestion;
                if(question) {
                    let entry = question.entries.find((v) => {
                        return this.checkHydraMatch(v, resp.choice)
                    });
                    (question as any)['value'] = entry.id;
                }
            }
        }
        this.questions = result;
        NProgress.done();
    }


    async handleQuestionChange(question: MultipleChoiceQuestion|TextBlockQuestion) {

        let target: any = {};
        let q: any = question;
        if (q.value) {
            target[q.id] = q.value;
        }

        this.save({session: this.session, page: +this.$router.currentRoute.params['page'], payload: target})
            .then((session: QuizSession) => {
                if (session.submittedAt) {
                    this.$router.push({name: 'app.home'});
                } else {
                    this.$router.push({
                        name: 'app.session.page',
                        params: {['page']: session.currentPage + ''}
                    }, () => {
                        this.update();
                    });
                }
                NProgress.done();
            }).catch((err) => {
            NProgress.done();
            this.hydraErrorWithNotify(err)

        });
    }

    getValues() {
        let target: any = {};
        this.questions["hydra:member"].forEach(function (q: any) {
            if (q.value) {
                target[q.id] = q.value;
            }
        });
        return target;
    }

    orderEntries(entries: MultipleChoiceEntry[]) {
        return _.orderBy(entries, function (o) {
            return o.order;
        })
    }


    mapCharacterIndex(index: number) {
        const lookup = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        return lookup[index];
    }


    submitResults() {
        let result = this.getValues();
        NProgress.start();
        this.submit({session: this.session, page: +this.$router.currentRoute.params['page'], payload: result})
            .then((session: QuizSession) => {
                if (session.submittedAt) {
                    this.$router.push({name: 'app.home'});
                } else {
                    this.$router.push({
                        name: 'app.session.page',
                        params: {['page']: session.currentPage + ''}
                    }, () => {
                        this.update();
                    });
                }
                NProgress.done();
            }).catch((err) => {
            NProgress.done();
            this.hydraErrorWithNotify(err)

        });
    }
}

</script>

<style lang="scss">
    .question_statement {
        margin-bottom: 1rem;
    }
    .multiple-choice-entry{
        width: 100%;
        margin-bottom: 5px;
        position: relative;
        .el-radio__label{
            width: 100%;
            display: block;
            word-wrap: break-word;
            white-space: normal;
            padding-left: 20px;
        }
        .el-radio__input{
            position: absolute;
            top: 0;
            left: 0;
        }
    }
</style>
