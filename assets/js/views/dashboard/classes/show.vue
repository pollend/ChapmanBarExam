<template>
    <div class="section">
        <div class="is-size-4">Info</div>
        <class-form :classroom="classroom"></class-form>
        <el-divider></el-divider>
        <div class="is-size-4">Access Control</div>
        <quiz-access-form :classroom="classroom"></quiz-access-form>
    </div>
</template>

<script lang="ts">
    import {Component, Provide, Vue} from "vue-property-decorator";
    import ClassForm from './component/ClassForm'
    import QuizAccessForm from './component/QuizAccessForm';
    import service from "../../../utils/request";
    import Classroom from "../../../entity/classroom";

    @Component({
        components: {ClassForm,QuizAccessForm}
    })
    export default class ShowClass extends Vue{
        @Provide() classroom: Classroom = null;

        async created(){
            const response = await service({
                url: '/_api/classrooms/' + this.$router.currentRoute.params['class_id'],
                method: 'get'
            });
            this.classroom = response.data;
        }

    }

    // export default {
    //     name: 'ShowClass',
    //     components: {ClassForm,QuizAccessForm},
    //     data() {
    //         return {
    //             access: {
    //                 loading: false,
    //                 count: 0,
    //                 data: null
    //             }
    //         };
    //     },
    //     methods: {
    //         async loadAccessData(queryInfo) {
    //             this.access.loading = true;
    //             // const response = await getQuizAccess(this.$router.currentRoute.params.class_id);
    //             // const {quiz_access} = response.data;
    //             // quiz_access.forEach(function (e) {
    //             //     e.range = [e.open_date,e.close_date];
    //             // });
    //             // this.access.data = quiz_access;
    //             // this.access.loading = false;
    //         }
    //     }
    // };
</script>

<style rel="stylesheet/scss" lang="scss">
</style>