<template>
    <div class="section">
        <div class="is-size-4">Info</div>
        <class-form></class-form>
        <el-divider></el-divider>
        <div class="is-size-4">Access Control</div>
        <quiz-access-form :class-id="$router.currentRoute.params.class_id"></quiz-access-form>
    </div>
</template>

<script>
    import ClassForm from './component/ClassForm';
    import {getQuizAccess} from "@/api/quiz-access";
    import QuizAccessForm from "./component/QuizAccessForm";
    export default {
        name: 'ShowClass',
        components: {ClassForm,QuizAccessForm},
        data() {
            return {
                access: {
                    loading: false,
                    count: 0,
                    data: null
                }
            };
        },
        methods: {
            async loadAccessData(queryInfo) {
                this.access.loading = true;
                const response = await getQuizAccess(this.$router.currentRoute.params.class_id);
                const {quiz_access} = response.data;
                quiz_access.forEach(function (e) {
                    e.range = [e.open_date,e.close_date];
                });
                this.access.data = quiz_access;
                this.access.loading = false;
            }
        }
    };
</script>

<style rel="stylesheet/scss" lang="scss">
</style>