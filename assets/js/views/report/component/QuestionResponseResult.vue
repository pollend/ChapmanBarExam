<template>
    <div v-if="questions != null">
        <div v-for="(q,index) in questions['hydra:member']" v-bind:key="q.id" v-bind:id="`question-${q.id}`">
            <div class="tag-container">
                <!-- el-tag elements clogging up event queue when unbinding replaced with span for performance -->
                <span v-for="tag in q.tags" :key="tag.id" class="el-tag el-tag--info el-tag--medium el-tag--light">{{tag.name}}</span>
                <!--                <el-tag :disableTransitions="true" type="info" v-for="tag in q.tags" :key="tag.id">{{tag.name}}</el-tag>-->
            </div>
            <template v-if="q['@type']=== 'MultipleChoiceQuestion'">
                <p class="question_statement"  v-bind:class="{'question-selected': question_mark ===  `question-${q.id}` }">
                    {{index }}. {{q.content}}
                </p>
                <template>
                    <div v-for="(e,index) in orderEntries(q.entries)"  :key="e.id" >
                        ({{mapCharacterIndex(index)}})
                        <template v-if="q['@id'] in questionResponse">
                            <multiple-choice-selection :type="checkHydraMatch(questionResponse[q['@id']].choice,e) ? (questionResponse[q['@id']].correctResponse? 'correct': 'in-correct') : ''"  :content="e.content"></multiple-choice-selection>
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
    import {Prop, Provide} from "vue-property-decorator";
    import Component, {mixins} from "vue-class-component";
    import {ValidateMix} from "../../../mixxins/validate-mix";
    import {HydraCollection, HydraMixxin} from "../../../entity/hydra";
    import {MultipleChoiceEntryMixxin, QuizQuestion} from "../../../entity/quiz-question";
    import {MultipleChoiceResponse} from "../../../entity/quiz-response";
    import MultipleChoiceSelection from "./MultipleChoiceSelection";
    import _ from "lodash";

    @Component({components: {MultipleChoiceSelection}})
    export default class QuestionResponseResult extends mixins(ValidateMix,HydraMixxin,MultipleChoiceEntryMixxin){
        @Prop() questions: HydraCollection<QuizQuestion>;
        @Prop() responses: HydraCollection<MultipleChoiceResponse>;
        @Provide() question_mark: string = '';

        get questionResponse() : any {
            return _.keyBy(this.responses["hydra:member"], (response) => {
                return response.question;
            });
        }
    }
</script>
