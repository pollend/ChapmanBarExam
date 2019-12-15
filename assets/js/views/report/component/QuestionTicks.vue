<template>
    <div>
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
    </div>
</template>

<script lang="ts">
    import {Prop, Provide, Vue} from "vue-property-decorator";
    import Component, {mixins} from "vue-class-component";
    import _ from "lodash";
    import {HydraCollection, hydraID} from "../../../entity/hydra";
    import {MultipleChoiceEntryMixxin, MultipleChoiceQuestion, QuizQuestion} from "../../../entity/quiz-question";
    import {MultipleChoiceResponse} from "../../../entity/quiz-response";

    @Component
    export default class QuestionTicks extends mixins(MultipleChoiceEntryMixxin) {
        @Prop() questions: HydraCollection<QuizQuestion>;
        @Prop() responses: HydraCollection<MultipleChoiceResponse>;

        get chunkedRespones() : any {
            return _.chunk(_.chunk(_.filter( this.questions["hydra:member"], function (q) {
                return q['@type'] === 'MultipleChoiceQuestion';
            }), 5), 10)
        }

        get questionResponse() : any {
            return _.keyBy(this.responses["hydra:member"], (response) => {
                return response.question;
            });
        }

        responseCharacter(question: MultipleChoiceQuestion) {
            const choice: MultipleChoiceResponse = (question["@id"] in this.questionResponse) ? this.questionResponse[question["@id"]] : null;

            if (choice) {
                if (choice.correctResponse === true) {
                    return '-'
                } else {
                    for (const [index, e] of this.orderEntries(question.entries).entries()) {
                        if (hydraID(e) == hydraID(choice.choice)) {
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
    .overview-table {
        width: 100%;

        td.response-entry, th.response-entry {
            text-align: center;
        }
    }
</style>
