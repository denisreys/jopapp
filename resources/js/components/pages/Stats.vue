<template>
    <div class="wrapper wrapper--stats">
        <div class="sb sb--l">
            <calendar
                v-if="data.calendar"
                :data="data"
                />
            <navigation/>
        </div>
        <div class="container">
            <cards
                v-if="data.calendar"
                :fullData="data"
                :selectedData="dataBySelectedDate"
            />
            <div class="container">
                <div class="sb sb--r">
                    <doneList
                        v-if="data.calendar"
                        :selectedData="dataBySelectedDate"
                    />
                </div>
                <div class="container">
                    <pointsDiagram
                        v-if="data.calendar"
                        :fullData="data"
                        :selectedData="dataBySelectedDate"
                    />
                    <groupsDiagram
                        v-if="data.calendar"
                        :selectedData="dataBySelectedDate"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import navigation from '../blocks/Navigation.vue';
import calendar from '../blocks/Statistics/StatisticsCalendar.vue';
import cards from '../blocks/Statistics/StatisticsCards.vue';
import pointsDiagram from '../blocks/Statistics/StatisticsPointsDiagram.vue';
import groupsDiagram from '../blocks/Statistics/StatisticsGroupsDiagram.vue';
import doneList from '../blocks/Statistics/StatisticsDoneList.vue';
import loader from '../blocks/Loader.vue';

    export default {
        data() {
            return {
                data: [],
                dataBySelectedDate: [],
                date: {
                    month: Number,
                    year: Number
                },
                components: 5,
            }
        },
        methods: {
            statisticsUpdate(){
                axios.post('/getstatistics', {page: 'stats'})
                .then((r) => {
                    this.data = r.data;
                    this.dataBySelectedDate = this.data.calendar[r.data.year].months[r.data.month];
                    this.date.year = r.data.year;
                    this.date.month = r.data.month;
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
            this.$root.loading.components = this.components;
            this.statisticsUpdate();
        },
        components: {
            navigation, calendar, cards, pointsDiagram, groupsDiagram, loader, doneList
        },
        name: 'Stats'
    }
</script>