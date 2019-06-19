import {Hydra} from "./hydra";

export default interface QuizSession extends Hydra{
    id: number,
    owner: string,
    quiz: string,
    classroom: string,
    responses: string[],
    maxScore: number,
    meta: string,
    score: string,
    submittedAt: string,
    currentPage: number,
    updatedAt: string,
    createdAt: string
}