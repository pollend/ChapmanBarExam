<template>
    <div>
        <div class="is-size-4">Info</div>
        <class-form></class-form>
        <el-divider></el-divider>
        <div class="is-size-4">Access Control</div>
        <quiz-access-form></quiz-access-form>
    </div>
</template>

<script lang="ts">

import {Component, Vue} from "vue-property-decorator";
import ClassForm from "./component/ClassForm.vue";
import QuizAccessForm from "./component/QuizAccessForm.vue";

@Component({
    components: {ClassForm,QuizAccessForm}
})
export default class ClassesGeneral extends Vue{
}
</script>