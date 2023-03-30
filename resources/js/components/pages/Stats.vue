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
                v-if="data.calendar"
                :fullData="data"
                :selectedData="dataBySelectedDate"
            />
            <div class="sb sb--r">
                <doneList
                    v-if="data.calendar"
                    :selectedData="dataBySelectedDate"
                />
            </div>
            <div class="main">
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
    <loader 
      v-if="components > loaded"
      :loading="{components: components, loaded: loaded}" 
    />
</template>
<script>
import axios from 'axios';
import navigation from '../blocks/Navigation.vue';
import calendar from '../blocks/Statistics/StatisticCalendar.vue';
import cards from '../blocks/Statistics/StatisticsCards.vue';
import pointsDiagram from '../blocks/Statistics/StatisticsPointsDiagram.vue';
import groupsDiagram from '../blocks/Statistics/StatisticsGroupsDiagram.vue';
import doneList from '../blocks/Statistics/StatisticDoneList.vue';
import loader from '../blocks/Loader.vue';

    export default {
        props: {
            loaded: Number
        },
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