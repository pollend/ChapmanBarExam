<template>
    <div>
        <el-form ref="form" :model="form">
            <el-form-item label="Open And Close">
                <el-date-picker
                        v-model="form.range"
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
import {mixins} from "vue-class-component";
import {HydraMixxin} from "../../../../entity/hydra";

export interface QuizCreateAccessForm {
    closeDate: string,
    isHidden: boolean,
    numAttempts: number,
    classroom: Classroom;
    openDate: string,
    quiz: Quiz | string,
    range: ["",""]
}

@Component({
    components: {ExamSearch}
})
export default class CreateQuizAccessForm extends mixins(HydraMixxin) {
    @Prop() readonly classroom: Classroom;
    @Prop() visible: boolean;

    @Provide() quizzes: [];
    @Provide() quizLoading: boolean;

    @Provide() form: QuizCreateAccessForm = {
        closeDate: "",
        isHidden: false,
        numAttempts: 0,
        openDate: "",
        quiz: null,
        classroom: this.classroom,
        range: ['', '']
    };

    async queryQuizzes(query: string) {
        this.quizLoading = true;
        service({
            url: ''
        });
        this.quizLoading = false;
    }

    clearForm() {
        this.form = {
            numAttempts: 0,
            closeDate: "",
            isHidden: false,
            openDate: "",
            quiz: null,
            classroom: this.classroom,
            range: ['', '']
        };
    }

    handleCancel() {
        this.$emit('cancel');
        this.clearForm();
    }

    async handleSubmit() {
        const response = await service({
            url: '/_api/quiz_accesses',
            method: 'POST',
            data: {
                classroom: this.hydraID(this.classroom),
                openDate: this.form.range[0],
                closeDate: this.form.range[1],
                isHidden: this.form.isHidden,
                quiz: this.hydraID(this.form.quiz),
                numAttempts: this.form.numAttempts
            }
        });
        console.log(response.data);
        this.clearForm();
        this.$emit('submit', response.data);
    }
}
</script>