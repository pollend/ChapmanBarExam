<template>
    <div class="section">
        <div class="container" v-if="questions">
            <div v-for="(q,index) in questions['hydra:member']" v-bind:key="q.id">
                <template v-if="q['@type'] === 'MultipleChoiceQuestion'">
                    <p class="question_statement">
                        {{index}}. {{q.content}}
                    </p>
                    <template>
                        <el-radio-group v-model="q.value">
                            <div v-for="(e,index) in orderEntries(q.entries)"  :key="e.id" >
                                <el-radio :label="e.id">({{mapCharacterIndex(index)}}) {{ e.content }}</el-radio>
                            </div>
                        </el-radio-group>
                    </template>
                </template>
                <template v-else-if="q['@type'] === 'TextBlockQuestion'">
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
import { mapGetters } from 'vuex';
import NProgress from 'nprogress';
import {Component, Provide, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";
import User from "../../entity/user";
import QuizSession from "../../entity/quiz-session";
import service from "../../utils/request";
import {HydraCollection} from "../../entity/hydra";
import {MultipleChoiceEntry, MultipleChoiceQuestion, TextBlockQuestion} from "../../entity/quiz-question";
import _ from 'lodash';

const authModule = namespace('auth');
const quizSessionModel = namespace('app/user-quiz-session');

@Component
export default class ShowQuizPage extends Vue {
    @authModule.Getter("user") user: User;
    @quizSessionModel.Getter("session") session: QuizSession;
    @quizSessionModel.Action("submit") submit: ({}) => Promise<QuizSession>;
    @Provide() questions: HydraCollection<MultipleChoiceQuestion | TextBlockQuestion> = null;

    created() {
       this.query();
    }

    query(){
        NProgress.start();
        service({
            url: '/_api/questions/sessions/' + this.session.id + '/page/' + this.$router.currentRoute.params['page'] + '/',
            method: 'GET'
        }).then((response) => {
            this.questions = response.data;
            NProgress.done();
        }).catch((err) => {
            console.log(err);

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
        this.submit({session: this.session , page:  +this.$router.currentRoute.params['page'], payload: result})
            .then((session:QuizSession) => {
                if(session.submittedAt){
                    this.$router.push({name:'app.home'});
                }
                else {
                    this.$router.push({
                        name: 'app.session.page',
                        params: {['page']: session.currentPage + ''}
                    }, () => {
                        this.query();
                    });
                }
                NProgress.done();
            }).catch((err) => {

        });
    }
}

</script>

<style>
    .question_statement {
        margin-bottom: 1rem;
    }
</style>
