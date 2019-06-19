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

        <div v-for="(q,index) in questions['hydra:member']" v-bind:key="q.id" v-bind:id="`question-${q.id}`">
            <div class="tag-container">
                <!-- el-tag elements clogging up event queue when unbinding replaced with span for performance -->
                <span v-for="tag in q.tags" :key="tag.id" class="el-tag el-tag--info el-tag--medium el-tag--light">{{tag.name}}</span>
<!--                <el-tag :disableTransitions="true" type="info" v-for="tag in q.tags" :key="tag.id">{{tag.name}}</el-tag>-->
            </div>
            <template v-if="q['@type']=== 'MultipleChoiceQuestion'">
                <p class="question_statement"  v-bind:class="{'question-selected': question_mark ===  `question-${q.id}` }">
                    {{index + 1}}. {{q.content}}
                </p>
                <template>
                    <div v-for="(e,index) in orderEntries(q.entries)"  :key="e.id" >
                        ({{mapCharacterIndex(index)}})
                        <template v-if="q['@id'] in questionResponse">
                            <multiple-choice-selection :type="choiceMatch(questionResponse[q['@id']].choice,e) ? (questionResponse[q['@id']].correctResponse? 'correct': 'in-correct') : ''"  :content="e.content"></multiple-choice-selection>
                        </template>
                        <template v-else>
                            <multiple-choice-selection :content="e.content"></multiple-choice-selection>
                        </template>
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

<script lang="ts">
import {Vue, Component, Provide} from "vue-property-decorator";
import service from "../../utils/request";
import {HydraCollection, hydraGetID} from "../../entity/hydra";
import {MultipleChoiceEntry, MultipleChoiceQuestion, QuizQuestion} from "../../entity/quiz-question";
import {MultipleChoiceResponse, QuizQuestionResponse} from "../../entity/quiz-response";
import NProgress from 'nprogress';
import _ from 'lodash';
import MultipleChoiceSelection from "./component/MultipleChoiceSelection.vue";

@Component({components: {MultipleChoiceSelection}})
export default class ReportShowOverview extends Vue {
    @Provide() questions: HydraCollection<QuizQuestion> = null;
    @Provide() responses: HydraCollection<MultipleChoiceResponse> = null;
    @Provide() started_loading: [];
    @Provide() question_mark: string = '';

    async created() {
        NProgress.start();
        await Promise.all([service({
            url: '/_api/quiz_responses/session/' + this.$router.currentRoute.params['report_id'],
            method: 'GET'
        }).then((response) => {
            this.responses = response.data;
        }).catch((err) => {

        }),
        service({
            url: '/_api/questions/sessions/' + this.$router.currentRoute.params['report_id'],
            method: 'GET'
        }).then((response) => {
            this.questions = response.data;
        }).catch((err) => {

        })]);
        NProgress.done();
    }

    get chunkedRespones() {
        return _.chunk(_.chunk(_.filter(this.questions["hydra:member"], function (q) {
            return q['@type'] === 'MultipleChoiceQuestion';
        }), 5), 10)
    }

    get questionResponse() {
        return _.keyBy(this.responses["hydra:member"], (response) => {
            return response.question;
        });
    }

    mapCharacterIndex(index: number) {
        const lookup = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        return lookup[index];
    }

    orderEntries(entries: MultipleChoiceEntry[]) {
        return _.orderBy(entries, function (o) {
            return o.order;
        })
    }

    choiceMatch(c1: MultipleChoiceEntry, c2: MultipleChoiceEntry): boolean {
        return hydraGetID(c1) == hydraGetID(c2);
    }

    responseCharacter(question: MultipleChoiceQuestion) {
        const choice: MultipleChoiceResponse = (question["@id"] in this.questionResponse) ? this.questionResponse[question["@id"]] : null;

        if (choice) {
            if (choice.correctResponse === true) {
                return '-'
            } else {
                for (const [index, e] of this.orderEntries(question.entries).entries()) {
                    if (hydraGetID(e) == hydraGetID(choice.choice)) {
                        return this.mapCharacterIndex(index);
                    }
                }
            }
        } else {
            return '_';
        }
        return '?';
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
        .overview-table {
            width: 100%;

            td.response-entry, th.response-entry {
                text-align: center;
            }
        }

        min-height: 40rem;
    }

</style>
