<template>
    <cv-data-table v-on:sort="onSortAction" title="Classes" :sortable="true" :columns="columns"  ref="table">
        <template slot="data">

            <cv-data-table-row
                    v-for="(row, rowIndex) in data"
                    :key="`row:${rowIndex}`"
                    :value="`${rowIndex}`"
                    ref="dataRows">
                <cv-data-table-cell
                        v-for="(cell, colIndex) in row"
                        :key="`cell:${colIndex}:${rowIndex}`">
                    <template v-if="colIndex == 'View'">
                        <cv-button @click="select(cell)" type="button" style="width: 100%;">View</cv-button>
                    </template>
                    <template v-else>
                        {{ cell }} - {{colIndex}}
                        test
                    </template>
                </cv-data-table-cell>
            </cv-data-table-row>
        </template>
        <template slot="batch-actions" v-if="type == 'active'">
            <cv-button @click="">
                Archive
                <TrashCan16 class="bx--btn__icon"/>
            </cv-button>
        </template>
    </cv-data-table>

</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';
    import utility from '../../utility'
    export default {
        name: 'classDatatable',
        props: ['routes', 'type'],
        data() {
            let mappings = {
                "Name": "name",
                "Created At": "created_at",
                "Students": "students",
                "View": ""
            };
            return {
                "mappings": mappings,
                "columns": _.keys(mappings),
                "sort": {},
                "data": [],
            };
        },
        methods: {
            onSortAction: function (event) {
                let key = this.mappings[_.keys(this.mappings)[event.index]];
                if (key)
                    this.sort[key] = event.order;
                this.query();
            },
            select: function (url) {
                window.location.href = url;
            },
            query: function () {

                let _this = this;
                axios.get(this.routes, {
                    params: {
                        sort: _this.sort//_.map(_this.sort, (value,key) => (key + ":" + value)).join(',')
                    }
                }).then(function (response) {
                    _this.data = utility.mapDatatable(response.data, _this.mappings);
                });
            }
        },
        mounted() {
            this.query();
        }
    }
</script>

<style>

</style>