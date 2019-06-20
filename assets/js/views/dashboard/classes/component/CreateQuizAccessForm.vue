<template>
    <el-dialog title="Add Access" :visible.sync="visible" :before-close="handleClose">
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
            <el-form-item>
                <el-input-number v-model="form.numAttempts" :min="0" ></el-input-number>
            </el-form-item>

            <el-form-item>
                <el-select
                        v-model="value"
                        multiple
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Please enter a keyword"
                        :remote-method="queryQuizzes"
                        :loading="loading">
                    <el-option
                            v-for="item in quizzes"
                            :key="item.id"
                            :label="item.name"
                            :value="item['@id']">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>
    </el-dialog>
</template>

<script lang="ts">
    import {Component, Prop, Provide, Vue} from "vue-property-decorator";
    import Classroom from "../../../../entity/classroom";
    import {Quiz} from "../../../../entity/quiz";
    import service from "../../../../utils/request";

    interface QuizAccessForm {
        closeDate: string,
        isHidden: boolean,
        numAttempts: number,
        classroom: Classroom;
        openDate: string,
        quiz: Quiz
    }

    @Component
    export default class CreateQuizAccessForm extends Vue{
        @Prop() readonly classroom: Classroom;
        @Prop() visible: boolean;
        @Provide() range: [string,string] = ["",""]

        @Provide() quizzes: [];
        @Provide() quizLoading: boolean;

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
            })
            this.quizLoading = false;
        }

        handleClose(done : () => void) {

        }


    }

</script>