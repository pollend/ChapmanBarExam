<template>
    <div class="section">
        <div class="container">
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

const authModule = namespace('auth')

@Component
export default class StartQuiz extends Vue {
    @authModule.Getter("user") user: User;

    startQuiz() {
        service({
            url: '/_api/quiz_sessions/start',
            method: 'post',
            data: {
                'access_id': +this.$router.currentRoute.params['quiz_access_id'],
                'user_id': this.user.id
            }
        }).then((response) => {
            const session: QuizSession = response.data;
            this.$router.push({
                name: 'app.session.page',
                params: {['page']: session.currentPage + ''}
            });


        }).catch((err) => {

        });

    }
}

</script>

<style>
</style>
