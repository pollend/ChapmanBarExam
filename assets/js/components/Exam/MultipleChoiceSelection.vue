<template>
    <div class="multiple-choice-entry">
        <span class="el-radio__input" v-bind:class="radioStyling">
            <span class="el-radio__inner"></span>
            {{ content }}
        </span>
    </div>
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

<style  lang="scss" scoped>
    .is-checked.is-correct .el-radio__inner{
        border-color: #00a20a;
        background: #009e43;
    }

    .is-checked.is-incorrect .el-radio__inner{
        border-color: #ff0000;
        background: #ff0059;
    }

    .multiple-choice-entry{
        width: 100%;
        margin-bottom: 5px;
        /*.el-radio__label{*/
        /*    width: 100%;*/
        /*    display: block;*/
        /*    word-wrap: break-word;*/
        /*    white-space: normal;*/
        /*    padding-left: 20px;*/
        /*}*/
        .el-radio__input{
            width: 100%;
            display: block;
            word-wrap: break-word;
            white-space: normal;
            padding-left: 20px;
        }
    }

</style>
