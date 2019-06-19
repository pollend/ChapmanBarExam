import {Hydra} from "./hydra";

export default interface QuizAccess  extends Hydra{
    id: number;
    numAttempts: number;
    openDate: string;
    closeDate: string;
    isHidden: boolean;
    isOpen: boolean;
    quiz: string | QuizAccess;
}