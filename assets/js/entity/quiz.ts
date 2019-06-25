import {Hydra} from "./hydra";
import {Timestamp} from "./timestamp";
import QuizSession from "./quiz-session";

export interface Quiz extends Hydra, Timestamp{
    description: string;
    id: number;
    name: string;
    numquestions: number,
    quizSessions: string | QuizSession[]

}