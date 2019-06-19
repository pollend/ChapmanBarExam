import {Hydra} from "./hydra";

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

export interface MultipleChoiceQuestion extends QuizQuestion {
    entries: MultipleChoiceEntry[];
    content: string;
}

export interface TextBlockQuestion extends QuizQuestion{
}