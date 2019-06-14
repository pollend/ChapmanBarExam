<template>
    <div class="section">
        <div class="container">
            <!-- $t is vue-i18n global function to translate lang -->
            <div class="app-container">
                <template v-if="classes === null">
                    <el-card v-for="i in [1,2,3,4]" v-bind:key="i" class="box-card class-box empty-box"></el-card>
                </template>
                <template v-else>
                    <el-card v-for="_class in classes" v-bind:key="_class.id" class="box-card class-box">
                        <h1 class="title"> {{_class.name}}</h1>
                        <el-row :gutter="20" v-for="(row,index) in group(_class.quizAccess)" v-bind:key="`quiz-${index}`">
                            <el-col :span="6"  v-for="access in row" v-bind:key="access.id">
                                <el-card class="box-card exam-card">
                                    <div slot="header" class="clearfix">
                                        <span> {{ access.quiz.name }}</span>
                                    </div>
                                    {{access.quiz.description}}
                                    <el-divider></el-divider>
                                    <el-row :gutter="20">
                                        <el-col :span="12" >
                                           {{ access.userAttempts}} / {{ access.numAttempts }}
                                        </el-col>
                                        <el-col :span="12" >
                                            {{ timestamp(access.openDate) }}</el-col>
                                    </el-row>

                                    <div class="foot">
                                        <router-link :to="{name: 'app.exam.start', params: {quiz_access_id: access.id} }">
                                            <el-button type="primary" style="width: 100%">Start</el-button>
                                        </router-link>
                                    </div>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-card>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import * as moment from 'moment';
import * as _ from 'lodash';
import NProgress from 'nprogress';
import {Component, Provide, Vue} from "vue-property-decorator";
// import User from "@/entity/user";
import {namespace} from "vuex-class";
import User from "../../entity/user";


const authModule = namespace('auth')

@Component
export default class Home extends Vue {
    @authModule.Getter("user") user: User;
    @Provide() classes: string = '';

    async created() {
        NProgress.start();
        // const response = await getClassesByUser(this.user.id);
        // this.classes = response.data.classes;
        NProgress.done();
    }

    static group(access: any) {
        return _.chunk(access, 4);
    };

    static timestamp(dateTime: any) {
        return moment(dateTime).fromNow();
    };
};
</script>

<style rel="stylesheet/scss" lang="scss">
   .class-box{
       margin-bottom: 1rem;

       .title{
           font-size: 1.2rem;
       }
       .exam-card{
           margin-bottom: .4rem;
       }
       .foot{
           margin-top: .5rem;
       }
       &.empty-box{
        height: 40rem;
       }
   }
</style>
