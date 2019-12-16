<template>
    <div>
        <el-button v-if="!edit" @click="edit = !edit"  size="mini" type="primary" icon="el-icon-edit"></el-button>
        <template v-if="edit">
            <el-form-item v-for="(entry,index) in question.entries" class="multiple-choice-entry-container">
                <el-row>
                    <el-col :span="2">
                        <div class="multiple-choice-entry-index">{{index}}.</div>
                    </el-col>
                    <el-col :span="21">
                        <el-input class="multiple-choice-entry" v-model="entry.content"></el-input>
                    </el-col>
                </el-row>
            </el-form-item>
        </template>
        <template v-else>
            <template v-if="question['@type']=== 'MultipleChoiceQuestion'">
                <p class="question_statement"  v-bind:class="{'question-selected': question_mark ===  `question-${question.id}` }">
                    {{index }}. {{question.content}}
                </p>
                <template>
                    <div v-for="(e,index) in orderEntries(question.entries)"  :key="e.id" >
                        ({{mapCharacterIndex(index)}})
                        <multiple-choice-selection :type="checkHydraMatch(question.correctEntry,e) ? 'correct': ''"  :content="e.content"></multiple-choice-selection>
                    </div>
                </template>
            </template>
            <template v-else-if="question.type === 'text_block'">
                <el-card class="box-card">
                    {{index + 1}}. {{question.content}}
                </el-card>
            </template>
            <template v-else-if="question.type === 'short_answer'">

            </template>
        </template>
        <el-divider></el-divider>
    </div>
</template>

<script lang="ts">
    import {Prop, Provide, Vue} from "vue-property-decorator";
    import Component, {mixins} from "vue-class-component";
    import {MultipleChoiceEntryMixxin, QuizQuestion} from "../../../../entity/quiz-question";
    import {ValidateMix} from "../../../../mixxins/validate-mix";
    import {HydraMixxin} from "../../../../entity/hydra";
    import MultipleChoiceSelection from "../../../../components/Exam/MultipleChoiceSelection.vue";


    @Component({components:{MultipleChoiceSelection}})
    export default class QuestionEntryPreview extends mixins(ValidateMix,HydraMixxin,MultipleChoiceEntryMixxin){
        @Prop() question: QuizQuestion
        @Prop() index: number

        @Provide() question_mark: string = '';
        @Provide() edit: boolean = false
    }
</script>
