import {Hydra} from "./hydra";
import {Quiz} from "./quiz";

export default interface QuizSession extends Hydra{
    id: number,
    owner: string,
    quiz: Quiz,
    classroom: string,
    responses: string[],
    meta: string,
    score: string,
    submittedAt: string,
    currentPage: number,
    updatedAt: string,
    createdAt: string
}
