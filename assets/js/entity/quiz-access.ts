import {Hydra} from "./hydra";

export default interface QuizAccess  extends Hydra{
    id: number;
    numAttempts: number;
    openDate: string;
    closeDate: string;
    quiz: string | QuizAccess;
}