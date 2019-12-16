<template>
    <div>
        <el-button v-if="!edit" @click="edit = !edit"  size="mini" type="primary" icon="el-icon-edit"></el-button>
        <template v-if="edit">
            <template v-if="question['@type']=== 'MultipleChoiceQuestion'">
                <el-form v-if="question">
                    <el-form-item>
                        <el-row>
                            <el-col :span="1">
                                <div class="multiple-choice-entry-index">{{index}}.</div>
                            </el-col>
                            <el-col :span="22">
                                <el-input :disabled="isLoading" v-model="question.content"  type="textarea"></el-input>
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item v-for="(entry,index) in  orderEntries(question.entries)" class="multiple-choice-entry-container">
                        <el-row>
                            <el-col :span="1">
                                <div class="multiple-choice-entry-index">{{index}}.</div>
                            </el-col>
                            <el-col :span="22">
                                <el-input  :disabled="isLoading" class="multiple-choice-entry" v-model="entry.content"></el-input>
                            </el-col>
                        </el-row>
                    </el-form-item>
                </el-form>
            </template>
            <el-row>
                <el-button  :disabled="isLoading" @click="cancel">Cancel</el-button>
                <el-button  :disabled="isLoading"  :loading="isLoading" @click="save">Save</el-button>
            </el-row>
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
    import {Model, Prop, Provide, Vue} from "vue-property-decorator";
    import Component, {mixins} from "vue-class-component";
    import {MultipleChoiceEntryMixxin, MultipleChoiceQuestion, QuizQuestion} from "../../../../entity/quiz-question";
    import {ValidateMix} from "../../../../mixxins/validate-mix";
    import {Hydra, HydraMixxin} from "../../../../entity/hydra";
    import MultipleChoiceSelection from "../../../../components/Exam/MultipleChoiceSelection.vue";
    import service from "../../../../utils/request";

    @Component({components:{MultipleChoiceSelection}})
    export default class QuestionEntryPreview extends mixins(ValidateMix,HydraMixxin,MultipleChoiceEntryMixxin){
        @Model() question: QuizQuestion | MultipleChoiceQuestion
        @Prop() index: number

        @Provide() question_mark: string = '';
        @Provide() edit: boolean = false
        @Provide() isLoading: boolean = false

        async cancel() {
            this.edit = false
            await this.refresh()
        }

        async refresh() {
            const result = await service({
                url: this.hydraID(this.question),
                method: 'GET'
            });
            Object.assign(this.question,result.data)
        }

        async save() {
            this.isLoading = true
            if(this.question['@type']=== 'MultipleChoiceQuestion') {
                const multipleChoiceQuestion : MultipleChoiceQuestion = <MultipleChoiceQuestion>this.question
                const response = await service({
                    url: this.hydraID(this.question),
                    method: 'PUT',
                    data: {
                        "content": multipleChoiceQuestion.content
                    }
                });

                for(let entry of multipleChoiceQuestion.entries){
                    await service({
                        url: this.hydraID(entry),
                        method: 'PUT',
                        data: {
                            "content":  entry.content
                        }
                    });
                }

                this.edit = false
                await this.refresh()
                this.isLoading = false
            }

        }
    }
</script>
