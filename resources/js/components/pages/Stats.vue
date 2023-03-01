<template>
    <div class="container container--dashboard">
        <div class="sb sb--l">
            <calendar 
                v-if="data.calendar"
                :data="data"
                />
            <navigation/>
        </div>
        <div class="main">
            <cards
                :fullData="data"
                :selectedData="dataBySelectedDate"
            />
            <div class="sb sb--r">1</div>
            <div class="main">
                <pointsDiagram 
                    v-if="dataBySelectedDate"
                    :fullData="data"
                    :selectedData="dataBySelectedDate"
                />
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import navigation from '../blocks/Navigation.vue';
import calendar from '../blocks/Statistics/StatisticCalendar.vue';
import cards from '../blocks/Statistics/StatisticsCards.vue';
import pointsDiagram from '../blocks/Statistics/StatisticsPointsDiagram.vue';

    export default {
        data() {
            return {
                data: [],
                dataBySelectedDate: []
            }
        },
        methods: {
            statisticsUpdate(){
                axios.get('/getstatistics')
                .then((r) => {
                    this.data = r.data;
                    this.dataBySelectedDate = this.data.calendar[r.data.year].months[r.data.month];
                });
            },
            updateSelectedDate(year, month){
                if(year && month)
                    this.dataBySelectedDate = this.data.calendar[year].months[month];
                else 
                    this.dataBySelectedDate = this.data.calendar[year];
            }
        },
        mounted() {
            this.statisticsUpdate();
        },
        components: {
            navigation, calendar, cards, pointsDiagram
        },
        name: 'Stats'
    }
</script>
<style lang="scss">
    @import './resources/sass/_variables.scss';

    
</style>