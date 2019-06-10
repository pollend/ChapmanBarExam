<template>
    <el-form ref="form" :model="form">
        <el-form-item label="Class Name">
            <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" >Save</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    export default {
        data() {
            return {
                form: {

                }
            }
        }
    }
</script>