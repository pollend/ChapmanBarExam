<template>
    <div style="height: 500px"></div>
</template>

<script>
    import {DonutChart,defaultColors} from '@carbon/charts'
    export default {
        name: 'reportScore',
        props: ['route'],
        data() {
          return {
              "chart": null
              // "chart" : new DonutChart(this.$el,{data: {
              //         labels: ["Quantity", "Leads", "Sold", "Restocking", "Misc"],
              //         datasets: [
              //             {
              //                 label: "Scores",
              //                 backgroundColors: [defaultColors[0]],
              //                 data: [65000, 29123, 35213, 51213, 16932]
              //             }
              //         ]
              //     },
              //     options: {
              //         centerLabel: 'Scores',
              //         centerNumber:'100'
              //     }})
          }
        },
        mounted() {
            this.query();

            // barChart.setData({
            //
            // })

        },
        methods: {
            query: function () {
                let _this = this;
                axios.get(this.route,{
                    params:{
                        sort: this.sort
                    }
                }).then(function (response) {
                    // _this.data = [];
                    let d = {
                        labels: ['correct','incorrect'],
                        datasets: [
                            {
                                backgroundColors: [defaultColors[0], defaultColors[1]],
                                data: [response.data.correct_count, response.data.max_count - response.data.correct_count]
                            }
                        ]
                    };
                    let o = {
                        centerLabel: 'Score',
                        centerNumber: response.data.correct_count + '/' + response.data.max_count
                    };
                    if(_this.chart == null){
                        _this.chart = new DonutChart(_this.$el,{
                            data: d,
                            options: o
                        })
                    }
                    // _this.chart.update();

                });
            },
            updateChart(){

            }
        }
    }
</script>