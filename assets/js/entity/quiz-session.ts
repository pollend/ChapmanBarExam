import {Hydra} from "./hydra";
import {Quiz} from "./quiz";
import User from "./user";
import {QuizQuestionResponse} from "./quiz-response";

export default interface QuizSession extends Hydra{
    id: number,
    owner: User,
    quiz: Quiz,
    classroom: string,
    responses: QuizQuestionResponse[],
    meta: string,
    score: string,
    submittedAt: string,
    currentPage: number,
    updatedAt: string,
    createdAt: string
}
