<template>
    <span class="el-radio__input" v-bind:class="radioStyling">
        <span class="el-radio__inner"></span>
        {{ content }}
    </span>
</template>

<script lang="ts">
    import {Vue, Component, Provide, Prop} from "vue-property-decorator";

    @Component
    export default class MultipleChoiceSelection extends Vue {
        @Prop() readonly type: string;
        @Prop() readonly content: string;

        get radioStyling() {

            switch (this.type) {
                case 'correct':
                    return {
                        'is-checked': true,
                        'is-correct': true,
                        'is-incorrect': false
                    };
                case 'in-correct':
                    return {
                        'is-checked': true,
                        'is-correct': false,
                        'is-incorrect': true
                    };

            }
        }

    }
</script>

<style>
    .is-checked.is-correct .el-radio__inner{
        border-color: #00a20a;
        background: #009e43;
    }

    .is-checked.is-incorrect .el-radio__inner{
        border-color: #ff0000;
        background: #ff0059;
    }
</style>