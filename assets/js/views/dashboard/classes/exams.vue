<template>
    <div>
        hello world
    </div>
</template>

<script lang="ts">

    import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../entity/classroom";
import service from "../../../utils/request";
import {HydraCollection} from "../../../entity/hydra";
import {Quiz} from "../../../entity/quiz";
import {FilterBuilder, SearchFilter} from "../../../utils/filter";

const classroomShowModule = namespace('dashboard/classroom/show')

@Component
export default class ClassesExams extends Vue {
    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() hydraCollection: HydraCollection<Quiz> = null;

    async loadCollection() {
        const builder = new FilterBuilder();
        builder.addFilter(new SearchFilter("quizSessions.classroom",this.classroom.id + ''));
        const response = await service({
            url: '/_api/quizzes?' + builder.build(),
            method: 'GET'
        });
        this.hydraCollection = response.data;
    }

    async created() {

    }

    @Watch('classroom')
    async onClassroomSet(val: Classroom, oldVal: Classroom) {
        // this.hasChanged = false;
        this.loadCollection()
    }
}

</script>