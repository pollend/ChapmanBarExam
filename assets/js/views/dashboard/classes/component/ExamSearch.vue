<template>
    <el-select
            :value="value ? value.name : ''"
            @input="update"
            filterable
            remote
            reserve-keyword
            placeholder="Please enter a keyword"
            :remote-method="remoteQuery"
            :loading="loading">
        <el-option
                v-for="item in (quizzes ? quizzes['hydra:member'] : [])"
                :key="item.id"
                :label="item.name"
                :value="item">
        </el-option>
    </el-select>
</template>

<script lang="ts">

import {Component, Prop, Provide, Vue} from "vue-property-decorator";
import service from "../../../../utils/request";
import {FilterBuilder, SearchFilter} from "../../../../utils/filter";
import {HydraCollection} from "../../../../entity/hydra";
import {Quiz} from "../../../../entity/quiz";

@Component
export default class ExamSearch extends Vue {
    @Prop() readonly value: Quiz | null;

    @Provide() loading: boolean = false;
    @Provide() quizzes: HydraCollection<Quiz> = null;

    async remoteQuery(query: string) {
        if (query !== '') {
            this.loading = true;
            const builder = new FilterBuilder();
            builder.addFilter(new SearchFilter('name', query));
            const response = await service({
                url: '/_api/quizzes?' + builder.build(),
                method: 'GET'
            });
            this.quizzes = response.data;
            this.loading = false;
        }
    }

    update(newValue: Quiz) {
        if (newValue != this.value) {
            this.$emit('change');
        }
        this.$emit('input', newValue);
    }
}
    
</script>