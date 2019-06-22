<template>
    <el-form v-if="classroom" ref="classroom" :model="classroom" :disabled="loading" >

        <el-form-item label="Class Name">
            <el-input v-model="classroom.name"></el-input>
        </el-form-item>
        <el-form-item label="Course Number">
            <el-input v-model="classroom.courseNumber"></el-input>
        </el-form-item>
        <el-form-item label="Description">
            <el-input type="textarea" v-model="classroom.description"></el-input>
        </el-form-item>
        <el-form-item>
            <el-button type="primary"  :loading="loading" @click="onSubmit" >Save</el-button>
        </el-form-item>
    </el-form>

</template>

<script lang="ts">

import {Component, Prop, Provide, Vue} from "vue-property-decorator";
import Classroom from "../../../../entity/classroom";
import service from "../../../../utils/request";
import {namespace} from "vuex-class";
const classroomShowModule = namespace('dashboard/classroom/show');

@Component
export default class ClassForm extends Vue {

    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() loading: boolean = false;

    async onSubmit() {
        this.loading = true;

        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id,
            method: 'PUT',
            data: {
                description: this.classroom.description,
                courseNumber: this.classroom.courseNumber,
                name: this.classroom.name
            }
        });
        Object.assign(this.classroom, response.data);
        this.loading = false;
    }
}
</script>

<style rel="stylesheet/scss" lang="scss">
</style>