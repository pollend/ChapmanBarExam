<template>
    <div class="report">
        <cv-data-table v-on:sort="onSort" title="Reports" :sortable="true" :columns="columns"  ref="table">
            <template slot="data">
                <cv-data-table-row v-for="(row, rowIndex) in data">
                    <cv-data-table-cell v-for="(cell, colIndex) in row" :key="`cell:${colIndex}:${rowIndex}`">
                        <template v-if="colIndex == 'View'">
                            <cv-button @click="select(cell)" type="button" style="width: 100%;">View</cv-button>
                        </template>
                        <template v-else>
                            {{ cell }}
                        </template>

                    </cv-data-table-cell>
                </cv-data-table-row>
            </template>
        </cv-data-table>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: 'reportComponent',
        props: ['route'],
        data() {
            return {
                "columns": [
                    "Score",
                    "Quiz",
                    "Submitted At",
                    "Elapsed Time",
                    "View"
                ],
                "sort":{},
                "data": []
            };
        },
        methods: {
            onSort: function (event) {
                console.log(event);
                this.sort[this.columns[event.index]] = event.order;
                this.query()
            },
            select: function(url) {
                window.location.href = url;
            },
            query:function () {
                let _this = this;
                axios.get(this.route,{
                    params:{
                        sort: this.sort
                    }
                }).then(function (response) {
                    _this.data = []
                    response.data.forEach(function(element){
                        _this.data.push({
                            'Score' :  element.score + "/" + element.max_score,
                            'Quiz' : element.quiz.name,
                            'Submitted At' : element.submitted_at,
                            'Elapsed Time' : '20 Min',
                            'View' : element.uri
                        })
                    })

                });
            }
        },
        mounted() {
            this.query();
        }
    }
</script>

<style>
    .report .bx--data-table-header{
        display:none;
    }
    .report .bx--table-toolbar{
        display:none;
    }

</style>