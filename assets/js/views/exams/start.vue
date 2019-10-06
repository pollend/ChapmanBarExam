<template>
    <div class="section">
        <div class="container">
            {{ access ? access.quiz.name : ''}}
            <el-divider></el-divider>
            <h3>Directions:</h3>
            <p>
            Each of the questions or incomplete statements below is followed by four suggested answers or completions. You are to choose the best of the stated alternatives. Answer all questions according to the generally accepted view, except where otherwise noted.
            For the purposes of this test, you are to assume that Articles 1 and 2 of the Uniform Commercial Code have been adopted. You are also to assume relevant application of Article 9 of the UCC concerning fixtures. The Federal Rules of Evidence are deemed to control. The terms "Constitution," "constitutional," and "unconstitutional" refer to the federal Constitution unless indicated to the contrary. You are also to assume that there is no applicable community property law, no guest statute, and no No-Fault Insurance Act unless otherwise specified. In negligence cases, if fault on the claimant's part is or may be relevant, the statement of facts for the particular question will identify the contributory or comparative negligence rule that is to be applied.
            </p>

            <p>goes at the first page of all exam, before the exam starts</p>
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
