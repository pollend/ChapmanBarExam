import {Hydra, HydraMixxin} from "./hydra";
import {Component, Mixins, Vue} from "vue-property-decorator";
import _ from "lodash";
import {mixins} from "vue-class-component";
import {deprecate} from "util";

export interface QuizQuestion extends Hydra {
    id: number;
    order: number;
    group:number;
}

export interface MultipleChoiceEntry  extends Hydra {
    content: string;
    id: number;
    order: number;
}

@Component
export class MultipleChoiceEntryMixxin extends mixins(HydraMixxin){
    characters(){
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
    }


    mapCharacterIndex(index: number) {
        return this.characters()[index];
    }

    orderEntries(entries: MultipleChoiceEntry[]) {
        return _.orderBy(entries, function (o) {
            return o.order;
        })
    }


    getIndex(entries:MultipleChoiceEntry[], entry: MultipleChoiceEntry): number{
        let value = null;
        let e = this.orderEntries(entries);

        for(let i = 0; i < e.length; i++){
            if(this.checkHydraMatch(e[i],entry)){
                return i;
            }
        }
        return null;
    }
}

export interface MultipleChoiceQuestion extends QuizQuestion {
    entries: MultipleChoiceEntry[];
    content: string;
}

export interface TextBlockQuestion extends QuizQuestion{
}