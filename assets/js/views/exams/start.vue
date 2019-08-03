<template>
    <div class="section">
        <div class="container">
            {{ access ? access.quiz.name : ''}}
            <el-divider></el-divider>
            This is some information about startting quiz
            <el-divider></el-divider>
            <el-button  @click="startQuiz"  type="primary" style="width: 100%">Begin</el-button>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Provide, Vue} from "vue-property-decorator";
import service from "../../utils/request";
import {namespace} from "vuex-class";
import User from "../../entity/user";
import QuizSession from "../../entity/quiz-session";
import QuizAccess from "../../entity/quiz-access";

const authModule = namespace('auth')
const quizSessionModel = namespace('app/user-quiz-session');

@Component
export default class StartQuiz extends Vue {
    @authModule.Getter("user") user: User;
    @quizSessionModel.Action("start") start: ({}) => Promise<QuizSession>;

    @Provide() access:QuizAccess = null;

    async created() {
        const response = await service({
            url: '/_api/quiz_accesses/' + this.$router.currentRoute.params['quiz_access_id'],
            method: 'GET'
        });
        this.access = response.data;
    }

    async startQuiz() {
        this.start({
            user: this.user,
            access: this.access
        }).then((result:QuizSession) => {
            this.$router.push({
                name: 'app.session.page',
                params: {['page']: result.currentPage + ''}
            })
        }).catch((err) => {

        });
    }
}

</script>

<style>
</style>
