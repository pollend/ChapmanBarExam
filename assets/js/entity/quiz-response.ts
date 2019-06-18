import {Hydra} from "./hydra";
import {MultipleChoiceEntry} from "./quiz-question";

export interface QuizQuestionResponse extends Hydra {
    id: number;
    session: string;
    question:string;
}

export interface MultipleChoiceResponse extends QuizQuestionResponse{
    choice: string | MultipleChoiceEntry;
    correctResponse: boolean;

}