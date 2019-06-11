<template>
    <el-form ref="form" :model="form" :disabled="loading" >
        <el-form-item label="Class Name">
            <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="Course Number">
            <el-input v-model="form.course_number"></el-input>
        </el-form-item>
        <el-form-item label="Description">
            <el-input type="textarea" v-model="form.description"></el-input>
        </el-form-item>
        <el-form-item>
            <el-button type="primary"  :loading="loading" @click="onSubmit" >Save</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import {getClass, patchClass} from "@/api/classes";

    export default {
        data() {
            return {
                form: {
                    name: ''
                },
                loading: false
            }
        },
        async created(){

            this.loading = true;
            const response = await getClass(this.$router.currentRoute.params.class_id);
            const {data} = response;
            this.handle(data);
            this.loading = false;

        },
        methods: {
            async onSubmit() {
                this.loading = true;
                const response = await patchClass(this.$router.currentRoute.params.class_id,  {
                    name: this.form.name,
                    description: this.form.description,
                    course_number: this.form.course_number
                });
                const {data} = response;
                this.handle(data);
                this.loading = false;
                this.$message({
                    message: 'Class Configuration Save.',
                    type: 'success'
                });
            },
            handle(payload){
                const {classroom} = payload;
                this.form = classroom;
            }
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss">
</style>