<template>
    <div class="section">
        <div class="container">
            <el-form v-if="classroom" ref="classroom_add_form" :model="classroom" :disabled="isLoading" >
                <el-form-item label="Class Name" prop="name">
                    <el-input v-model="classroom.name"></el-input>
                </el-form-item>
                <el-form-item label="Course Number">
                    <el-input v-model="classroom.courseNumber"></el-input>
                </el-form-item>
                <el-form-item label="Description">
                    <el-input type="textarea" v-model="classroom.description"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary"  :loading="isLoading" @click="onSubmit" >Create</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script lang="ts">

import {Component, Prop, Provide, Vue} from "vue-property-decorator";
import service from "../../../utils/request";
import {mixins} from "vue-class-component";
import {ValidateMix} from "../../../mixxins/validate-mix";
import {ElForm} from "element-ui/types/form";

@Component
export default class ClassesGeneral extends mixins(ValidateMix){
    @Provide()
    classroom: { courseNumber: string; name: string; description: string } = {
        courseNumber: "",
        description: "",
        name: ""
    };

    @Provide()
    isLoading: boolean = false;

    created() {

    }

    async onSubmit(){
        this.isLoading = true;
        const form: ElForm= <ElForm>this.$refs['classroom_add_form'];
        try {
            form.clearValidate();
            const response = await service({
                url: '/_api/classrooms',
                method: 'POST',
                data: {
                    description: this.classroom.description,
                    courseNumber: this.classroom.courseNumber,
                    name: this.classroom.name
                }
            });
            console.log(response);
            this.$router.push({'name':'dashboard.class','params':{'class_id' : response.data.id+''}})
        }
        catch (e) {
            this.hydraHandleForm(e,form);
        }
        this.isLoading= false;
    }

}

</script>

<style type="scss">

</style>