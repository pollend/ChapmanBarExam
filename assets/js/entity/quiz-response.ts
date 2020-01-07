import {Hydra} from "./hydra";
import {MultipleChoiceEntry, MultipleChoiceQuestion} from "./quiz-question";

export interface QuizQuestionResponse extends Hydra {
    id: number;
    session: string;
    question: MultipleChoiceQuestion;
}

export interface MultipleChoiceResponse extends QuizQuestionResponse{
    choice: MultipleChoiceEntry;
    correctResponse: boolean;
}
