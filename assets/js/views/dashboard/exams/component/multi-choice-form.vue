<template>
    <div>
        <el-form v-if="question">
            <el-form-item>
                <el-input  v-model="question.content"  :disabled="isLoading" type="textarea"></el-input>
            </el-form-item>
            <div>
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
            </div>
        </el-form>
        <el-row v-if="flagChange">
            <el-button>Cancel</el-button>
            <el-button>Save</el-button>
        </el-row>
    </div>

</template>

<script lang="ts">

    import {Component, Inject, Model, Prop, Provide, Vue, Watch} from "vue-property-decorator";
    import service from "../../../../utils/request";
    import {MultipleChoiceQuestion} from "../../../../entity/quiz-question";
    import {HydraMixxin} from "../../../../entity/hydra";
    import {mixins} from "vue-class-component";

    @Component
    export default class MultipleChoiceForm extends mixins(HydraMixxin) {
        @Model() question: MultipleChoiceQuestion;
        @Provide() isLoading: boolean = false;
        @Provide() flagChange:boolean = false
        // @Provide() response: MultipleChoiceQuestion = null;

        // async resyncQuestion(){
        //
        // }

        //
        // @Watch('question', { immediate: true})
        async onChildChanged(val: MultipleChoiceQuestion, oldVal: MultipleChoiceQuestion) {
            this.flagChange = true
            // console.log(val);
            // if(val) {
            //     this.isLoading = true;
            //     const response = await service({
            //         url: this.hydraID(val),
            //         method: 'GET'
            //     });
            //     // this.response = response.data;
            //     this.isLoading = false;
            // }
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
    .multiple-choice-entry-container{
        .multiple-choice-entry-index{
            text-align: right;
        }
        .multiple-choice-entry{
            margin-left: .5rem;
        }

    }
</style>
