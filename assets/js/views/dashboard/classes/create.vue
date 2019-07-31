<template>
    <div class="section">
        <div class="container">
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
                    <el-button type="primary"  :loading="loading" @click="onSubmit" >Create</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script lang="ts">

import {Component, Prop, Provide, Vue} from "vue-property-decorator";
import Classroom from "../../../entity/classroom";
import service from "../../../utils/request";
@Component
export default class ClassesGeneral extends Vue{
    @Provide()
    classroom: { courseNumber: string; name: string; description: string } = {
        courseNumber: "",
        description: "",
        name: ""
    };

    @Provide()
    loading: boolean = false

    async onSubmit(){
        this.loading = true;
        const response = await service({
            url: '/_api/classrooms',
            method: 'POST',
            data: {
                description: this.classroom.description,
                courseNumber: this.classroom.courseNumber,
                name: this.classroom.name
            }
        });
        this.loading= false;
        this.$router.push({'name':'dashboard.class','params':{'class_id' : response.data.id+''}})

    }

}

</script>

<style type="scss">

</style>