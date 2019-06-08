<template>
    <span class="el-radio__input" v-bind:class="radioStyling">
        <span class="el-radio__inner"></span>
        {{ entry.content }}
    </span>
</template>

<script>
    export default {
        name: 'MultipleChoiceEntry',
        props: ['correct_entry','entry','response'],
        methods: {
        },
        computed: {
            radioStyling() {
                let correct_response = this.entry.id === this.correct_entry.id;

                if(!this.response)
                    return   { 'is-checked': correct_response};

                let user_response_match = this.response.id === this.entry.id;

                return {
                    'is-checked': correct_response | user_response_match,
                    'is-correct': correct_response && user_response_match,
                    'is-incorrect': !correct_response && user_response_match
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