<template>
    <div>
        <el-progress v-if="!collection" :text-inside="true" :stroke-width="26" :percentage="progress"></el-progress>
        <el-divider v-if="!collection" content-position="center">{{message}}</el-divider>

        <template v-if="collection">
            <el-table :data="collection['hydra:member']">
               <el-table-column label="No." width="50">
                   <template slot-scope="scope">
                       {{(scope.$index + 1)}}
                   </template>
               </el-table-column>
               <el-table-column label="Correct Group Responses">
                   <el-table-column label="Total">
                       <template slot-scope="scope">
                           {{ percentHelper(getNumberOfResponses(scope.row,scope.row.correctChoice) / getAllSessions(scope.row).length)}}
                       </template>
                   </el-table-column>
                   <el-table-column label="Upper 27%">
                       <template slot-scope="scope">
                           <span :set="filteredRow = filterSessions(scope.row,getSessionByRange(getAllSessions(scope.row),0.27,false))">
                               {{ percentHelper(getNumberOfResponses(filteredRow,scope.row.correctChoice) / getAllSessions(filteredRow).length)}}
                           </span>
                       </template>
                   </el-table-column>
                   <el-table-column label="Lower 27%">
                       <template slot-scope="scope">
                           <span :set="filteredRow = filterSessions(scope.row,getSessionByRange(getAllSessions(scope.row),0.27,true))">
                               {{ percentHelper(getNumberOfResponses(filteredRow,scope.row.correctChoice) / getAllSessions(filteredRow).length)}}
                           </span>
                       </template>
                   </el-table-column>
               </el-table-column>
               <el-table-column label="Point Biserial"></el-table-column>
               <el-table-column label="Correct Answer">
                   <template slot-scope="scope">
                        {{getCharacter(scope.row.choices,scope.row.correctChoice)}}
                   </template>
               </el-table-column>
               <el-table-column label="Response Frequencies - 0 indicates correct answer">
                   <el-table-column width="40" label="0">
                       <template slot-scope="scope">
                           {{ scope.row.nonResponse.length }}
                       </template>
                   </el-table-column>
                   <el-table-column v-for="key in ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']" width="40" :label="key" :key="key">
                       <template slot-scope="scope">
                           <template v-if="getCharacter(scope.row.choices,scope.row.correctChoice) === key">
                               *
                           </template>
                           {{ getNumberOfResponsesByCharacter(scope.row,key) }}
                       </template>
                   </el-table-column>
               </el-table-column>
               <el-table-column label="Non Distractor">
               </el-table-column>
           </el-table>
        </template>
    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../../entity/classroom";
import service from "../../../../utils/request";
import {MultipleChoiceEntry, MultipleChoiceEntryMixxin} from "../../../../entity/quiz-question";
import QuizSession from "../../../../entity/quiz-session";
import {HydraCollection} from "../../../../entity/hydra";
import {mixins} from "vue-class-component";
import _ from "lodash";
import NProgress from "nprogress";

const classroomShowModule = namespace('dashboard/classroom/show');

interface ReportItem{
    choices: MultipleChoiceEntry[],
    correctChoice: MultipleChoiceEntry,
    responseByUser:  { [index:string] : QuizSession[] },
    nonResponse: QuizSession[]
}

@Component
export default class StandardItemReport extends mixins(MultipleChoiceEntryMixxin){
    @classroomShowModule.Getter('classroom') classroom: Classroom;
    @Provide() collection: HydraCollection<ReportItem> = null;

    @Provide() message = '';
    @Provide() progress = 0;

    percentHelper(value: number){
        return parseFloat((value * 100.0) + '').toFixed(2)
    }


    getAllSessions(item:ReportItem) : QuizSession[]{
        let items: QuizSession[] = [];
        for(const entry of Object.values<QuizSession[]>(item.responseByUser))
        {
            items = items.concat(entry)
        }
        items = items.concat(item.nonResponse);
        return items;
    }


    filterSessions(item:ReportItem, sessions:QuizSession[]){
        let byUser:  { [index:string] : QuizSession[] } = {};

        for(const [key,value] of Object.entries(item.responseByUser)) {
            byUser[key] = _.intersectionBy(value, sessions, function (o) {
                return o.id;
            });
        }

        let result: ReportItem = {
            choices: item.choices,
            correctChoice: item.correctChoice,
            responseByUser: byUser,
            nonResponse: <QuizSession[]>_.intersectionBy(item.nonResponse,sessions,function (o) {
                return o.id;
            })
        };
        return result;
    }

    getSessionByRange(sessions:QuizSession[],percent:number, flip:boolean) {
        const count = Math.round(sessions.length * percent);

        const result =  _.take(_.sortBy(sessions, [function (value: QuizSession) {
            return flip ? value.score : -value.score;
        }]), count);

        return result;
    }

    getNumberOfResponses(item: ReportItem,entry:MultipleChoiceEntry): number{
        const entries = this.orderEntries(item.choices);
        for(let i = 0; i< entries.length; i++){
            if(this.checkHydraMatch(entries[i],entry)){
                let key = this.hydraID(entries[i]);
                if(key  in item.responseByUser)
                    return item.responseByUser[key].length;
            }
        }
        return 0;
    }

    getNumberOfResponsesByCharacter(item: ReportItem,chatacter:string): number{
        const entries = this.orderEntries(item.choices);
        for(let i = 0; i< entries.length; i++){
            if(this.characters()[i] === chatacter){
                let key = this.hydraID(entries[i]);
                if(key  in item.responseByUser)
                    return item.responseByUser[key].length;
            }
        }
        return 0;
    }

    getCharacter(choices: MultipleChoiceEntry[],correct:MultipleChoiceEntry){
        const entries = this.orderEntries(choices);
        for(let i = 0; i< entries.length; i++){
            if(this.checkHydraMatch(entries[i],correct)){
                return this.characters()[i];
            }
        }
        return '-'
    }

    isCorrect(choices: MultipleChoiceEntry[],correct:MultipleChoiceEntry){
        const entries = this.orderEntries(choices);
        for(let i = 0; i< entries.length; i++){
            if(this.checkHydraMatch(entries[i],correct)){
                return true;
            }
        }
        return false;
    }

    async _query() : Promise<boolean>{
        NProgress.start();
        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/report/' + this.$route.params.report_id + '/item'
        });
        NProgress.done();

        let type = this.hydraType(response.data)
        if (type === 'hydra:Collection') {

            this.collection = response.data;
            return true;
        }
        return false;
    }

    async created(){

       if(await this._query() === false) {
           while (true) {
               this.message = "preparing"
               try {
                   await new Promise(resolve => setTimeout(() => resolve(), 1000));
                   const response = await service({
                       url: '/_api/classrooms/' + this.classroom.id + '/report/' + this.$route.params.report_id + '/item/status'
                   });
                   this.progress = response.data['percent']
                   this.message = response.data['message']
                   if (response.data['finished'] === true) {
                       break;
                   }
               } catch (e) {
                  return
               }
           }
           await this._query()
       }

    }
}
</script>
