<template>
    <div>
        <el-form ref="form" :model="form">
            <el-form-item label="Open And Close">
                <el-date-picker
                        v-model="range"
                        type="datetimerange"
                        start-placeholder="Start Date"
                        end-placeholder="End Date">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="Hidden">
                <el-checkbox  v-model="form.isHidden"></el-checkbox>
            </el-form-item>
            <el-form-item label="Number Of Attempts">
                <el-input-number v-model="form.numAttempts" :min="0" ></el-input-number>
            </el-form-item>

            <el-form-item label="Exam">
                <exam-search v-model="form.quiz"></exam-search>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button @click="handleCancel">Cancel</el-button>
            <el-button type="primary" @click="handleSubmit">Confirm</el-button>
        </span>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Provide, Vue} from "vue-property-decorator";
    import Classroom from "../../../../entity/classroom";
    import {Quiz} from "../../../../entity/quiz";
    import service from "../../../../utils/request";
    import ExamSearch from './ExamSearch';

    interface QuizAccessForm {
        closeDate: string,
        isHidden: boolean,
        numAttempts: number,
        classroom: Classroom;
        openDate: string,
        quiz: Quiz | string
    }

    @Component({
        components: {ExamSearch}
    })
    export default class CreateQuizAccessForm extends Vue{
        @Prop() readonly classroom: Classroom;
        @Prop() visible: boolean;
        @Provide() range: [string,string] = ["",""]

        @Provide() quizzes: [];
        @Provide() quizLoading: boolean;

        @Provide() createExam: () => Promise<boolean> = async function(){
          return false;
        };

        @Provide() form: QuizAccessForm = {
            closeDate: "",
            isHidden: false,
            numAttempts: 0,
            openDate: "",
            quiz: null,
            classroom: this.classroom
        };

        async queryQuizzes(query: string) {
            this.quizLoading = true;
            service({
                url: ''
            });
            this.quizLoading = false;
        }

        handleCancel() {
            this.$emit('cancel');
        }
        handleSubmit(){
            this.$emit('submit',this.createExam);
        }



    }

</script>